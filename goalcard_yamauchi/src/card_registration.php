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


// 新しいカードの登録処理
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create_card') {
    $cardName = $_POST['card_name'];
    $goalDate = $_POST['goal_date'];
    $rule = $_POST['rule'];
    $reward = $_POST['reward'];
    $userId = $user['id'];

    $checkCardSql = 'SELECT * FROM reward_card WHERE user_id = :user_id AND card_name = :card_name';
    $checkCardStmt = $pdo->prepare($checkCardSql);
    $checkCardStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $checkCardStmt->bindValue(':card_name', $cardName, PDO::PARAM_STR);
    $checkCardStmt->execute();

    if (!$checkCardStmt->fetch()) {
        $insertSql = 'INSERT INTO reward_card (user_id, card_name, total_point, goal_date, rule, reward, created_at) VALUES (:user_id, :card_name, 0, :goal_date, :rule, :reward, NOW())';
        $insertStmt = $pdo->prepare($insertSql);
        $insertStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $insertStmt->bindValue(':card_name', $cardName, PDO::PARAM_STR);
        $insertStmt->bindValue(':goal_date', $goalDate, PDO::PARAM_STR);
        $insertStmt->bindValue(':rule', $rule, PDO::PARAM_STR);
        $insertStmt->bindValue(':reward', $reward, PDO::PARAM_STR);

        if ($insertStmt->execute()) {
            $_SESSION['message'] = 'カードが作成されました。';
        } else {
            $_SESSION['message'] = 'カードの作成に失敗しました。';
        }
    } else {
        $_SESSION['message'] = '同じ名前のカードは既に存在します。';
    }

    header('Location: card_registration.php');
    exit();
}

// HTMLの表示部分でメッセージを表示
if (isset($_SESSION['message'])) {
    echo $_SESSION['message'];
    unset($_SESSION['message']);
}
?>
