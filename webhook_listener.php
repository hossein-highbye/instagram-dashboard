<?php
require_once 'db.php';

/*
 *
 * First of all this webhook URI should set in facebook app dashboard
 *
*/

// Verify webhook if requested
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['hub_challenge'])) {
    echo $_GET['hub_challenge'];
    exit;
}

// Handle POST requests for webhook events
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['entry'][0]['messaging'])) {
    // Process new messages
    foreach ($input['entry'][0]['messaging'] as $event) {
        if (isset($event['message'])) {
            // Extract message details
            $messageText = $event['message']['text'];
            $senderId = $event['sender']['id'];
            $timestamp = $event['timestamp'];
            $conversationId = $event['message']['mid'];

            // Store message in database
            $stmt = $pdo->prepare("INSERT INTO messages (conversation_id, sender_id, message_text, created_time, timestamp) VALUES (?, ?, ?, NOW(), ?)");
            $stmt->execute([$conversationId, $senderId, $messageText, $timestamp]);

            // Just logging for now
            error_log("New message from $senderId: $messageText at $timestamp");
        }
    }
}

http_response_code(200);