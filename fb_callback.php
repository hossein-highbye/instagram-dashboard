<?php
session_start();
require_once 'config.php';
require_once 'vendor/autoload.php';

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
    $accessToken = $helper->getAccessToken(FB_REDIRECT_URL);

    if (!isset($accessToken)) {
        // Error handling
        echo "Error: Login failed!";
        exit;
    }

    // Exchange for long-lived token
    $oAuth2Client = $fb->getOAuth2Client();
    $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);

    // Store the token in session or database
    $_SESSION['fb_access_token'] = (string) $longLivedAccessToken;

    // Redirect to the dashboard
    header('Location: admin_dashboard.php');
    exit;
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}