<?php
require_once 'config.php';
require_once 'vendor/autoload.php';

try {
    $fb = new \Facebook\Facebook([
        'app_id' => FB_APP_ID, // Replace with your app id
        'app_secret' => FB_APP_SECRET, // Replace with your app secret
        'default_graph_version' => 'v20.0',
    ]);
} catch (\Facebook\Exceptions\FacebookSDKException $e) {
    error_log($e->getMessage(),0, '/error-log.log');
}

$helper = $fb->getRedirectLoginHelper();
$permissions = ['pages_messaging', 'instagram_basic', 'instagram_manage_messages'];
$loginUrl = $helper->getLoginUrl(FB_REDIRECT_URL, $permissions);

// Redirect user to login
header('Location: ' . htmlspecialchars($loginUrl));
exit;