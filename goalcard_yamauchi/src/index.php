<?php
session_start();

$dbn ='mysql:dbname=mentalcare_yamauchi;charset=utf8mb4;port=3306;host=mysql57.mentalcare.sakura.ne.jp';
$user = 'mentalcare';
$pwd = 'nami202211';

try {
    $pdo = new PDO($dbn, $user, $pwd);
} catch (PDOException $e) {
    echo "データベースエラー: " . $e->getMessage();
    exit();
}

$email = isset($_SESSION['user_mail']) ? $_SESSION['user_mail'] : '';

if ($email) {
    $sql = 'SELECT * FROM user WHERE mail = :mail';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':mail', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $_SESSION['user_name'] = $user['name'];
    }
}

// セッション変数からユーザーIDを取得
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// ユーザー情報をデータベースから取得
if ($email) {
    $sql = 'SELECT * FROM user WHERE mail = :mail';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':mail', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $_SESSION['user_name'] = $user['name'];
        $userId = $user['id']; // ユーザーIDをセッションに設定
    }
}

// ユーザーカード一覧を取得（18ポイント未満のカードのみ）
if (isset($_POST['action']) && $_POST['action'] == 'fetch_cards') {
    $userId = $user['id'];
    $cardSql = 'SELECT * FROM reward_card WHERE user_id = :user_id AND total_point < 18';
    $cardStmt = $pdo->prepare($cardSql);
    $cardStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $cardStmt->execute();
    $cards = $cardStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($cards);
    exit;
}

// カードの詳細情報を取得する処理
if (isset($_POST['action']) && $_POST['action'] == 'get_reward_card_details') {
    $cardId = $_POST['card_id'];

    $cardSql = 'SELECT * FROM reward_card WHERE id = :card_id';
    $cardStmt = $pdo->prepare($cardSql);
    $cardStmt->bindValue(':card_id', $cardId, PDO::PARAM_INT);
    $cardStmt->execute();
    $cardDetails = $cardStmt->fetch(PDO::FETCH_ASSOC);

    if ($cardDetails) {
        echo json_encode($cardDetails);
    } else {
        echo json_encode(['error' => 'Card not found']);
    }
    exit;
}
// ポイントを加算する処理
if (isset($_POST['action']) && $_POST['action'] == 'add_points') {
    $cardId = $_POST['card_id'];
    $pointsToAdd = $_POST['points'];

    // reward_card テーブルの total_point を更新
    $updateSql = 'UPDATE reward_card SET total_point = total_point + :points WHERE id = :card_id';
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->bindValue(':points', $pointsToAdd, PDO::PARAM_INT);
    $updateStmt->bindValue(':card_id', $cardId, PDO::PARAM_INT);

    if ($updateStmt->execute()) {
        // point_history テーブルに記録を追加（もし使用している場合）
        $historySql = 'INSERT INTO point_history (card_id, point, updated_at) VALUES (:card_id, :point, NOW())';
        $historyStmt = $pdo->prepare($historySql);
        $historyStmt->bindValue(':card_id', $cardId, PDO::PARAM_INT);
        $historyStmt->bindValue(':point', $pointsToAdd, PDO::PARAM_INT);
        $historyStmt->execute();

        echo json_encode(['message' => 'Points and history added successfully']);
    } else {
        echo json_encode(['error' => 'Failed to add points']);
    }
}


// 18ポイントに達したカードを取得する処理
$sql = "
    SELECT rc.*, eh.exchange_date 
    FROM reward_card rc
    LEFT JOIN exchange_history eh ON rc.id = eh.card_id
    WHERE rc.user_id = :userId AND rc.total_point >= 18";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
$stmt->execute();
$completedCards = $stmt->fetchAll(PDO::FETCH_ASSOC);


// exchange_historyテーブルに新しいレコードを挿入
foreach ($completedCards as $card) {
    $cardId = $card['id'];
    $sql = "SELECT COUNT(*) AS count FROM exchange_history WHERE card_id = :cardId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':cardId', $cardId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] == 0) {
        $created_at = date('Y-m-d H:i:s');
        $deadline = date('Y-m-d H:i:s', strtotime($created_at . '+1 week'));
        $exchange_date = date('Y-m-d H:i:s');

        // デバッグ情報を出力
        var_dump($cardId, $created_at, $deadline, $exchange_date);

        $sql = "INSERT INTO exchange_history (card_id, created_at, deadline, exchange_date) 
                VALUES (:cardId, :created_at, :deadline, :exchange_date)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':cardId', $cardId, PDO::PARAM_INT);
        $stmt->bindValue(':created_at', $created_at, PDO::PARAM_STR);
        $stmt->bindValue(':deadline', $deadline, PDO::PARAM_STR);
        $stmt->bindValue(':exchange_date', $exchange_date, PDO::PARAM_STR);
        $stmt->execute();
    }
}


if (isset($_POST['action']) && $_POST['action'] == 'fetch_reward_cards') {
    $userId = $user['id']; // ログインしているユーザーのID
    $rewardCardSql = 'SELECT * FROM reward_card WHERE user_id = :user_id';
    $rewardCardStmt = $pdo->prepare($rewardCardSql);
    $rewardCardStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $rewardCardStmt->execute();
    $rewardCards = $rewardCardStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($rewardCards);
    exit;
}



?>
