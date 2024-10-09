<?php
session_start();
global $appID,$appSecret;
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
    $fb = new \JanuSoftware\Facebook\Facebook([
        'app_id' => $appID,
        'app_secret' => $appSecret,
        'default_graph_version' => 'v20.0',
    ]);
} catch (\JanuSoftware\Facebook\Exception\SDKException $e) {
    error_log(basename(__FILE__) . ' :Facebook connection prob!',3,'error-log.log');
}

try {
    // Get the conversations for the Instagram account
    $response = $fb->get("/$instagramAccountId/conversations?fields=id,participants,updated_time", $accessToken);
    $conversations = $response->getDecodedBody();

    // Display the conversation details
    foreach ($conversations['data'] as $conversation) {
        echo "Conversation ID: " . $conversation['id'] . "<br>";
        echo "Participants: " . implode(", ", array_column($conversation['participants']['data'], 'name')) . "<br>";
        echo "Last Updated: " . $conversation['updated_time'] . "<br>";
        echo "<hr>";
    }

    // Check for pagination
    if (isset($conversations['paging']['next'])) {
        echo "<a href='{$conversations['paging']['next']}'>Load more conversations</a>";
    }

} catch(\JanuSoftware\Facebook\Exception\ResponseException $e) {
    error_log('Graph returned an error: ' . $e->getMessage(),0, 'error-log.log');;
    exit;
} catch(\JanuSoftware\Facebook\Exception\SDKException $e) {
    error_log('Facebook SDK returned an error: ' . $e->getMessage(),0, 'error-log.log');
    exit;
}