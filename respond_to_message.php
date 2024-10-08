<?php
session_start();
global $appID, $appSecret;
require_once 'db.php';
require_once 'check_token_expiration.php'; // Ensure the token is valid

// Check if Instagram Account ID and access token are set
if (!isset($_SESSION['instagram_account_id']) || !isset($_SESSION['fb_access_token'])) {
    echo "Error: Instagram account ID or access token missing!";
    exit;
}

$instagramAccountId = $_SESSION['instagram_account_id'];
$accessToken = $_SESSION['fb_access_token'];

// Check if conversation ID and message are set
if (!isset($_POST['conversation_id']) || !isset($_POST['message'])) {
    echo "Error: Conversation ID or message missing!";
    exit;
}

$conversationId = $_POST['conversation_id'];
$messageText = $_POST['message'];

try {
    $fb = new \Facebook\Facebook([
        'app_id' => $appID,
        'app_secret' => $appSecret,
        'default_graph_version' => 'v20.0',
    ]);
} catch (\Facebook\Exceptions\FacebookSDKException $e) {
    error_log(basename(__FILE__) . ' : Facebook connection problem!', 0, 'error-log.log');
    exit;
}

try {
    // Send a message to the conversation
    $response = $fb->post("/$conversationId/messages", [
        'message' => $messageText
    ], $accessToken);

    // Response handling
    if ($response->getStatusCode() === 200) {
        // Log success
        error_log("Message sent successfully to conversation ID $conversationId", 0, 'success-log.log');
    } else {
        error_log('Failed to send message: ' . $response->getBody(), 0, 'error-log.log');
    }
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    error_log('Graph returned an error: ' . $e->getMessage(), 0, 'error-log.log');
    exit;
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    error_log('Facebook SDK returned an error: ' . $e->getMessage(), 0, 'error-log.log');
    exit;
}

// Redirect back to the conversation view
header("Location: view_conversation.php?conversation_id=" . urlencode($conversationId));
exit;