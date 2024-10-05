<?php
session_start();
require_once 'config.php';
require_once 'db.php';

// Ensure user is logged in and Instagram account is set
if (!isset($_SESSION['user_id']) || !isset($_SESSION['instagram_account_id'])) {
    echo "Error: User or Instagram account not set!";
    exit;
}

$userId = $_SESSION['user_id'];
$instagramAccountId = $_SESSION['instagram_account_id'];

// Retrieve token from the database
$stmt = $pdo->prepare("SELECT access_token, expires_at FROM instagram_tokens WHERE user_id = ? AND instagram_account_id = ?");
$stmt->execute([$userId, $instagramAccountId]);
$tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

if ($tokenData) {
    $accessToken = $tokenData['access_token'];
    $expiresAt = $tokenData['expires_at'];

    // Check if the token is expired
    if (time() >= $expiresAt) {
        // Token has expired, request the user to log in again
        echo "Error: Access token has expired. Please log in again.";
        header('Location: login_with_facebook.php');
        exit;
    } else {
        // Token is still valid, set session variables for the request
        $_SESSION['fb_access_token'] = $accessToken;
        $_SESSION['fb_token_expiration'] = $expiresAt;
    }
} else {
    echo "Error: No access token found for this user!";
    exit;
}

// Token is valid, proceed with your requests
echo "Access token is still valid. Proceed with your requests.";