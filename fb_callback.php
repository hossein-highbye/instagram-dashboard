<?php
session_start();
require_once 'config.php';
require_once 'vendor/autoload.php';
require_once 'db.php';

try {
    $fb = new \Facebook\Facebook([
        'app_id' => FB_APP_ID,
        'app_secret' => FB_APP_SECRET,
        'default_graph_version' => 'v20.0',
    ]);
} catch (\Facebook\Exceptions\FacebookSDKException $e) {
    error_log($e->getMessage(),0, '/error-log.log');
}

$helper = $fb->getRedirectLoginHelper();

try {
    // Get the short-lived access token
    $accessToken = $helper->getAccessToken(FB_REDIRECT_URL);

    if (!isset($accessToken)) {
        echo "Error: Login failed!";
        exit;
    }

    // Exchange the short-lived token for a long-lived one
    $oAuth2Client = $fb->getOAuth2Client();
    $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);

    // Store token in session
    $_SESSION['fb_access_token'] = (string)$longLivedAccessToken;
    $_SESSION['fb_token_expiration'] = time() + $longLivedAccessToken->getExpiresAt()->getTimestamp();

    // Save token and expiration into the database
    $userId = $_SESSION['user_id']; // Assuming user is already logged in
    $instagramAccountId = $_SESSION['instagram_account_id'];
    $token = (string)$longLivedAccessToken;
    $expiresAt = $_SESSION['fb_token_expiration'];

    // Check if a token already exists for the user and Instagram account
    $stmt = $pdo->prepare("SELECT id FROM instagram_tokens WHERE user_id = ? AND instagram_account_id = ?");
    $stmt->execute([$userId, $instagramAccountId]);
    if ($stmt->rowCount() > 0) {
        // Update the existing token
        $stmt = $pdo->prepare("UPDATE instagram_tokens SET access_token = ?, expires_at = ? WHERE user_id = ? AND instagram_account_id = ?");
        $stmt->execute([$token, $expiresAt, $userId, $instagramAccountId]);
    } else {
        // Insert a new token record
        $stmt = $pdo->prepare("INSERT INTO instagram_tokens (user_id, instagram_account_id, access_token, expires_at) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $instagramAccountId, $token, $expiresAt]);
    }

    // Redirect to the dashboard or wherever necessary
    header('Location: get_instagram_account.php');
    exit;
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    error_log('Graph returned an error: ' . $e->getMessage(),0,'error-log.log');
    exit;
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    error_log('Facebook SDK returned an error: ' . $e->getMessage(),0,'error-log.log');
    exit;
}