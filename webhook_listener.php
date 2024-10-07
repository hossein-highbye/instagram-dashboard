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
    foreach ($input['entry'][0]['messaging'] as $event) {
        if (isset($event['message'])) {
            // Extract message details
            $messageText = $event['message']['text'];
            $senderId = $event['sender']['id'];
            $timestamp = $event['timestamp'];
            $conversationId = $event['sender']['id']; // Use the sender's ID as conversation_id (assuming 1-to-1 chat)

            // Insert message into the database
            $stmt = $pdo->prepare("INSERT INTO messages (conversation_id, sender_id, message_text, created_time, timestamp) VALUES (:conversation_id, :sender_id, :message_text, NOW(), :timestamp)");
            $stmt->execute([
                ':conversation_id' => $conversationId,
                ':sender_id' => $senderId,
                ':message_text' => $messageText,
                ':timestamp' => $timestamp
            ]);
        }
    }
}

http_response_code(200);