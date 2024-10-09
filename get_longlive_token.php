<?php
require_once 'db.php';
global $appID, $appSecret, $shortLivedToken;

// Prepare the cURL request to get a long-lived access token
$tokenExchangeUrl = "https://graph.facebook.com/v20.0/oauth/access_token?" . http_build_query([
        'grant_type' => 'fb_exchange_token',
        'client_id' => $appID,
        'client_secret' => $appSecret,
        'fb_exchange_token' => $shortLivedToken,
    ]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tokenExchangeUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'cURL error: ' . curl_error($ch);
    exit;
}

curl_close($ch);

// Decode the response
$responseData = json_decode($response, true);
echo '<pre>';
var_dump($responseData);
echo '</pre>';

// Check for errors in the response
if (isset($responseData['error'])) {
    echo 'Error during token generation: ' . $responseData['error']['message'];
    exit;
}

// Extract the long-lived access token
$longLivedAccessToken = $responseData['access_token'];

// Store the long-lived token in your database
//$stmt = $pdo->prepare("INSERT INTO instagram_tokens (user_id, instagram_account_id, access_token, expires_at)
//                       VALUES (:user_id, :instagram_account_id, :access_token, :expires_at)
//                       ON DUPLICATE KEY UPDATE access_token = :access_token, expires_at = :expires_at");
//
//$stmt->execute([
//    ':user_id' => YOUR_USER_ID, // Set the appropriate user ID
//    ':instagram_account_id' => YOUR_INSTAGRAM_ACCOUNT_ID, // Set the appropriate Instagram account ID
//    ':access_token' => $longLivedAccessToken,
//    ':expires_at' => time() + 60 * 60 * 24 * 60 // Assuming the token lasts for 60 days
//]);

echo "Long-lived token generated and stored successfully!";