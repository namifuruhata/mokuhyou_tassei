<?php
session_start();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$dbn ='mysql:dbname=mentalcare_yamauchi;charset=utf8mb4;port=3306;host=mysql57.mentalcare.sakura.ne.jp';
$user = 'mentalcare';
$pwd = 'nami202211';

try {
    $pdo = new PDO($dbn, $user, $pwd);
} catch (PDOException $e) {
    echo "データベースエラー: " . $e->getMessage();
    exit();
}

// LINEログインプロセスから得られるユーザーIDはすでにセッションに保存されていると仮定
$lineUserId = $_SESSION['user_id'];
var_dump($lineUserId); // ここでユーザーIDを確認


// LINEログイン成功後、ユーザーのLINE IDをセッションに保存
$_SESSION['user_id'] = $lineUserId;


// 初回ログイン時、LINE IDをデータベースに保存し、ユーザーIDをセッションにも保存
$sql = 'SELECT * FROM user WHERE line_id = :line_id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':line_id', $lineUserId, PDO::PARAM_STR);
var_dump($lineUserId); // ここで挿入するデータを確認
$stmt->execute();
$userRecord = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userRecord) {
    $insertSql = 'INSERT INTO user (line_id) VALUES (:line_id)';
    $insertStmt = $pdo->prepare($insertSql);
    $insertStmt->bindValue(':line_id', $lineUserId, PDO::PARAM_STR);
    $insertStmt->execute();
    $userId = $pdo->lastInsertId();
    $_SESSION['user_id'] = $userId;
} else {
   $_SESSION['user_id'] = $userRecord['id'];
}

// reward_cardテーブルへのレコード追加処理
$userId = $_SESSION['user_id'];

// 新しいカードの登録処理
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create_card') {
    $cardName = $_POST['card_name'];
    $goalDate = $_POST['goal_date'];
    $rule = $_POST['rule'];
    $reward = $_POST['reward'];

    if ($userId) {
        $checkCardSql = 'SELECT * FROM reward_card WHERE user_id = :user_id AND card_name = :card_name';
        $checkCardStmt = $pdo->prepare($checkCardSql);
        $checkCardStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $checkCardStmt->bindValue(':card_name', $cardName, PDO::PARAM_STR);
        $checkCardStmt->execute();

        if (!$checkCardStmt->fetch()) {
            $insertSql = 'INSERT INTO reward_card (user_id, card_name, goal_date, rule, reward, created_at) VALUES (:user_id, :card_name, :goal_date, :rule, :reward, NOW())';
            $insertStmt = $pdo->prepare($insertSql);
            $insertStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $insertStmt->bindValue(':card_name', $cardName, PDO::PARAM_STR);
            $insertStmt->bindValue(':goal_date', $goalDate, PDO::PARAM_STR);
            $insertStmt->bindValue(':rule', $rule, PDO::PARAM_STR);
            $insertStmt->bindValue(':reward', $reward, PDO::PARAM_STR);

            if ($insertStmt->execute()) {
                  var_dump("Insert successful"); // ここで実行結果を確認
                $_SESSION['message'] = 'カードが作成されました。';
            } else {
                  var_dump("Insert failed"); // ここでエラーを確認
                $_SESSION['message'] = 'カードの作成に失敗しました。';
            }
        } else {
            $_SESSION['message'] = '同じ名前のカードは既に存在します。';
        }
    } else {
        $_SESSION['message'] = 'ユーザーIDが見つかりません。ログインしてください。';
    }

    header('Location: card_registration.php');
    exit();
}
?>
