<?php
session_start();
require_once 'config.php';
require_once 'db.php';
require_once 'check_token_expiration.php'; // Ensure token is valid

// Check if Instagram Account ID and access token are set
if (!isset($_SESSION['instagram_account_id']) || !isset($_SESSION['fb_access_token'])) {
    echo "Error: Instagram account ID or access token missing!";
    exit;
}

$instagramAccountId = $_SESSION['instagram_account_id'];
$accessToken = $_SESSION['fb_access_token'];

try {
    $fb = new \Facebook\Facebook([
        'app_id' => FB_APP_ID,
        'app_secret' => FB_APP_SECRET,
        'default_graph_version' => 'v14.0',
    ]);
} catch (\Facebook\Exceptions\FacebookSDKException $e) {
    error_log($e->getMessage(), 0, 'error-log.log');
}

try {
    // Get the Instagram direct messages (conversations)
    $response = $fb->get("/$instagramAccountId/conversations", $accessToken);
    $conversations = $response->getDecodedBody();

    // Display conversation details
    foreach ($conversations['data'] as $conversation) {
        echo "Conversation ID: " . $conversation['id'] . "<br>";
        echo "Participants: " . implode(", ", array_column($conversation['participants']['data'], 'name')) . "<br>";
        echo "<hr>";
    }
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    error_log('Graph returned an error: ' . $e->getMessage(),0,'error-log.log');
    exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    error_log('Facebook SDK returned an error: ' . $e->getMessage(),0,'error-log.log');
    exit;
}