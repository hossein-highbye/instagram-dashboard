<?php
require_once 'db.php';
global $appID, $appSecret, $accessToken;

$url = "https://graph.facebook.com/v20.0/oauth/access_token?" . http_build_query([

    ]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$messages = json_decode($response, true);

if (isset($messages)) {
    echo '<pre>' . json_encode($messages) . '</pre>';
} else {
    echo json_encode(['error' => 'No messages found or an error occurred.']);
}