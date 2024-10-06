<?php
require_once 'config.php';

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
            $message = $event['message']['text'];
            $senderId = $event['sender']['id'];
            $timestamp = $event['timestamp'];

            // Store message in database or notify the dashboard
            // just a logging for now
            error_log("New message from $senderId: $message at $timestamp");
        }
    }
}

http_response_code(200);