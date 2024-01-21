<?php
// login.php

session_start();

// 安全な乱数を生成
$state = bin2hex(random_bytes(16));

// 生成した乱数をセッションに保存
$_SESSION['oauth_state'] = $state;

// LINEログインのための設定
$clientId = '2002792085'; // LINE Developersコンソールから取得したチャネルID
$redirectUri = urlencode('https://mentalcare.sakura.ne.jp/goalcard_yamauchi/src/index.html'); // コールバックURLをURLエンコード

// LINEログインURLの生成
$loginUrl = "https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id={$clientId}&redirect_uri={$redirectUri}&state={$state}&scope=profile";

?>


<!DOCTYPE html>
<html>
<head>
        <link rel="stylesheet" href="style.css">
    <title>LINEログイン</title>

</head>
<body>
    <a href="<?php echo htmlspecialchars($loginUrl); ?>" class="login-button">LINEでログイン</a>
</body>
</html>
