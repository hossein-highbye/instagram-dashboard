<?php
session_start();
global $appID;
require_once 'vendor/autoload.php';
require_once 'db.php';

try {
    $fb = new \Facebook\Facebook([
        'app_id' => $appID,
        'app_secret' => FB_APP_SECRET,
        'default_graph_version' => 'v20.0',
    ]);
} catch (\Facebook\Exceptions\FacebookSDKException $e) {
    error_log(basename(__FILE__) . ' :Facebook connection prob!', 3, '/path/to/error-log.log');
}

// Retrieve user ID and Instagram account ID from session

$userId = $_SESSION['user_id']; // Assuming the user is already logged in
$instagramAccountId = $_SESSION['instagram_account_id'];

// Check if a long-lived access token exists for this user and Instagram account
$stmt = $pdo->prepare("SELECT access_token, expires_at FROM instagram_tokens WHERE user_id = ? AND instagram_account_id = ?");
$stmt->execute([$userId, $instagramAccountId]);
$tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

if ($tokenData) {
    // Check if the token is near expiration (e.g., less than a day remaining)
    $currentTime = time();
    $expirationTime = $tokenData['expires_at'];

    if ($expirationTime > $currentTime && ($expirationTime - $currentTime) > 86400) { // Token is valid and has more than 24 hours left
        // Use the existing token since it is still valid
        $_SESSION['fb_access_token'] = $tokenData['access_token'];
        $_SESSION['fb_token_expiration'] = $expirationTime;
    } else {
        // Token is near expiration or expired, refresh it
        refreshAccessToken($fb, $tokenData['access_token'], $userId, $instagramAccountId);
    }
} else {
    // No token found, request a new one using Facebook OAuth login flow
    getNewAccessToken($fb);
}

/**
 * Refreshes the long-lived access token.
 */
function refreshAccessToken($fb, $accessToken, $userId, $instagramAccountId) {
    global $pdo;

    try {
        // Get a new long-lived token using the existing token
        $oAuth2Client = $fb->getOAuth2Client();
        $newLongLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);

        // Update token expiration time
        $expiresAt = $newLongLivedAccessToken->getExpiresAt()->getTimestamp();

        // Update token in the database
        $stmt = $pdo->prepare("UPDATE instagram_tokens SET access_token = ?, expires_at = ? WHERE user_id = ? AND instagram_account_id = ?");
        $stmt->execute([(string)$newLongLivedAccessToken, $expiresAt, $userId, $instagramAccountId]);

        // Update the session with the new token
        $_SESSION['fb_access_token'] = (string)$newLongLivedAccessToken;
        $_SESSION['fb_token_expiration'] = $expiresAt;
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        error_log('Error refreshing token: ' . $e->getMessage(), 3, '/path/to/error-log.log');
    }
}

/**
 * Redirects the user to get a new access token via Facebook login.
 */
function getNewAccessToken($fb) {
    $helper = $fb->getRedirectLoginHelper();
    $permissions = ['pages_messaging', 'instagram_basic', 'instagram_manage_messages']; // Required permissions
    $loginUrl = $helper->getLoginUrl(FB_REDIRECT_URL, $permissions);

    // Redirect user to login
    header('Location: ' . htmlspecialchars($loginUrl));
    exit;
}
