<?php
require_once 'db.php';
global $appID, $appSecret;

// Fetch the current token from the database
$accessToken = $_SESSION['fb_access_token'];

// Get a new long-lived access token before the current one expires
try {
    $fb = new \Facebook\Facebook([
        'app_id' => $appID,
        'app_secret' => $appSecret,
        'default_graph_version' => 'v20.0',
    ]);
} catch (\Facebook\Exceptions\FacebookSDKException $e) {
    error_log(basename(__FILE__) . ' :Facebook connection prob!',3,'error-log.log');
}

try {
    $response = $fb->get('/oauth/access_token', [
        'grant_type' => 'fb_exchange_token',
        'client_id' => $appID,
        'client_secret' => $appSecret,
        'fb_exchange_token' => $accessToken,
    ]);

    $newAccessToken = $response->getDecodedBody()['access_token'];

    // Save the new token in the database
    $stmt = $pdo->prepare("UPDATE tokens SET access_token = :access_token WHERE id = 1");
    $stmt->execute(['access_token' => $newAccessToken]);

    echo "Token refreshed successfully!";
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    echo 'Graph returned an error: ' . $e->getMessage();
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
}