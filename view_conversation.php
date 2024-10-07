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

// Fetch messages from the database
$stmt = $pdo->prepare("SELECT * FROM messages WHERE conversation_id = :conversation_id ORDER BY created_time ASC");
$stmt->execute([':conversation_id' => $conversationId]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h1>Conversation ID: $conversationId</h1>";

// Check if there are any messages
if (empty($messages)) {
    echo "<p>No messages found for this conversation.</p>";
} else {
    // Display messages
    foreach ($messages as $message) {
        echo "<p><strong>" . htmlspecialchars($message['sender_name']) . ":</strong> " . htmlspecialchars($message['message_text']) . " <em>(" . htmlspecialchars($message['created_time']) . ")</em></p>";
        echo "<hr>";
    }
}

// Check for pagination (if there are more messages to load)
if (isset($messages['paging']['next'])) {
    echo "<a href='{$messages['paging']['next']}'>Load more messages</a>";
}
?>

<!-- Reply form -->
<form action="respond_to_message.php" method="POST">
    <input type="hidden" name="conversation_id" value="<?php echo $conversationId; ?>">
    <textarea name="message" placeholder="Type your reply..." required></textarea><br>
    <button type="submit">Send Reply</button>
</form>