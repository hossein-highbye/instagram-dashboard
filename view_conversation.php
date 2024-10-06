<?php
session_start();
require_once 'db.php';
require_once 'check_token_expiration.php'; // Token validation

// conversation_id existence check
if (!isset($_GET['conversation_id'])) {
    echo "Error: Conversation ID missing!";
    exit;
}

$conversationId = $_GET['conversation_id'];
$accessToken = $_SESSION['fb_access_token'];

try {
    $fb = new \Facebook\Facebook([
        'app_id' => FB_APP_ID,
        'app_secret' => FB_APP_SECRET,
        'default_graph_version' => 'v20.0',
    ]);
    try {
        // Get the messages in the conversation
        $response = $fb->get("/$conversationId/messages?fields=from,message,created_time", $accessToken);
        $messages = $response->getDecodedBody();

        echo "<h1>Conversation ID: $conversationId</h1>";

        // Display messages
        foreach ($messages['data'] as $message) {
            echo "<p><strong>" . $message['from']['name'] . ":</strong> " . $message['message'] . " <em>(" . $message['created_time'] . ")</em></p>";
            echo "<hr>";
        }

        // Check for pagination (if there are more messages to load)
        if (isset($messages['paging']['next'])) {
            echo "<a href='{$messages['paging']['next']}'>Load more messages</a>";
        }

    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
} catch (\Facebook\Exceptions\FacebookSDKException $e) {
    error_log(basename(__FILE__) . ' :Facebook connection prob!',0,'error-log.log');
}
?>

<!-- Reply form -->
<form action="respond_to_message.php" method="POST">
    <input type="hidden" name="conversation_id" value="<?php echo $conversationId; ?>">
    <textarea name="message" placeholder="Type your reply..." required></textarea><br>
    <button type="submit">Send Reply</button>
</form>