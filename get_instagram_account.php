<?php
session_start();
global $appID;
require_once 'config.php';
require_once 'vendor/autoload.php';

// Check if access token is available in session
if (!isset($_SESSION['fb_access_token'])) {
    echo "Error: Facebook access token not found!";
    exit;
}

$accessToken = $_SESSION['fb_access_token'];

try {
    $fb = new \Facebook\Facebook([
        'app_id' => $appID,
        'app_secret' => FB_APP_SECRET,
        'default_graph_version' => 'v20.0',
    ]);
} catch (\Facebook\Exceptions\FacebookSDKException $e) {
    error_log(basename(__FILE__) . ' :Facebook connection prob!',3,'error-log.log');
}

try {
    // Get the list of pages the user manages
    $response = $fb->get('/me/accounts', $accessToken);
    $pages = $response->getDecodedBody();

    // Loop through pages to find Instagram business account
    foreach ($pages['data'] as $page) {
        if (isset($page['instagram_business_account'])) {
            $instagramAccountId = $page['instagram_business_account']['id'];

            // Store the Instagram Business Account ID in session
            $_SESSION['instagram_account_id'] = $instagramAccountId;
            echo "Instagram Business Account ID: " . $instagramAccountId . "<br>";
            echo "<a href='fetch_instagram_messages.php'>Fetch Instagram Messages</a>"; // Link to next step
        } else {
            echo "No Instagram business account linked to page: " . $page['name'] . "<br>";
        }
    }
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    error_log('Graph returned an error: ' . $e->getMessage(),0, '/error-log.log');
    exit;
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    error_log('Facebook SDK returned an error: ' . $e->getMessage(),0, '/error-log.log');
    exit;
}