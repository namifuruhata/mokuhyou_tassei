<?php
// token.php

$postData = array(
    'grant_type'    => 'authorization_code',
    'code'          => $_GET['code'], // LINEから渡される認証コード
    'redirect_uri'  => 'https://mentalcare.sakura.ne.jp/goalcard_yamauchi/src/index.html',
    'client_id'     => '2002792085',
    'client_secret' => 'bdcea7f3d00993a5e1515a1910158136',
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
curl_setopt($ch, CURLOPT_URL, 'https://api.line.me/oauth2/v2.1/token');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$json = json_decode($response);
$accessToken = $json->access_token; // アクセストークンを取得

// ここで必要に応じてアクセストークンを保存する処理を追加
?>
