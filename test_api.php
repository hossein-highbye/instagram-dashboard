<?php
global $appID, $appSecret;
require_once 'db.php';

$url = "https://graph.facebook.com/oauth/access_token?client_id={$appID}&client_secret={$appSecret}&grant_type=client_credentials";

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Curl Error: ' . curl_error($ch);
}
curl_close($ch);

$data = json_decode($response, true);

if (isset($data['access_token'])) {
    echo "Access Token: " . $data['access_token'];
} else {
    echo "Access code did not receive" . $response;
}