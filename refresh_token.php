<?php
require_once 'db.php';
require_once 'vendor/autoload.php';
global $appID, $appSecret;

try {
    $fb = new \Facebook\Facebook([
        'app_id' => $appID,
        'app_secret' => $appSecret,
        'default_graph_version' => 'v20.0',
    ]);
} catch (\Facebook\Exceptions\FacebookSDKException $e) {
    error_log(basename(__FILE__) . ' :Facebook connection problem!', 3, 'error-log.log');
}

// Fetch all tokens that will expire soon (within the next 24 hours)
$stmt = $pdo->prepare("SELECT user_id, instagram_account_id, access_token, expires_at FROM instagram_tokens WHERE expires_at < UNIX_TIMESTAMP(NOW()) + 86400");
$stmt->execute();
$tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($tokens as $tokenData) {
    $accessToken = $tokenData['access_token'];
    $userId = $tokenData['user_id'];
    $instagramAccountId = $tokenData['instagram_account_id'];

    try {
        // Get a new long-lived access token before the current one expires
        $response = $fb->get('/oauth/access_token', [
            'grant_type' => 'fb_exchange_token',
            'client_id' => $appID,
            'client_secret' => $appSecret,
            'fb_exchange_token' => $accessToken,
        ]);

        $newAccessToken = $response->getDecodedBody()['access_token'];
        $expiresAt = time() + (60 * 60 * 24 * 60); // Assuming a 60-day validity for long-lived tokens

        // Update the new token and expiration time in the database
        $updateStmt = $pdo->prepare("UPDATE instagram_tokens SET access_token = ?, expires_at = ? WHERE user_id = ? AND instagram_account_id = ?");
        $updateStmt->execute([$newAccessToken, $expiresAt, $userId, $instagramAccountId]);

        echo "Token refreshed successfully for User ID: $userId\n";
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        error_log('Graph returned an error: ' . $e->getMessage(), 3, 'error-log.log');
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        error_log('Facebook SDK returned an error: ' . $e->getMessage(), 3, 'error-log.log');
    }
}