<?php
// profile.php

// ここで$accessTokenを取得する処理を記述（セッションやデータベースから取得等）

$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $accessToken));
curl_setopt($ch, CURLOPT_URL, 'https://api.line.me/v2/profile');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$json = json_decode($response);
$userInfo = json_decode(json_encode($json), true); // ログインユーザ情報を取得

// ここで必要に応じてユーザ情報を保存または処理
?>

