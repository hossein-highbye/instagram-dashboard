<?php
global $appID, $appSecret;
require_once 'config.php';
require_once 'vendor/autoload.php';

try {
    $fb = new \JanuSoftware\Facebook\Facebook([
        'app_id' => $appID,
        'app_secret' => $appSecret,
        'default_graph_version' => 'v20.0',
    ]);
} catch (\JanuSoftware\Facebook\Exception\SDKException $e) {
    error_log(basename(__FILE__) . ' :Facebook connection prob!',3,'error-log.log');
}

$helper = $fb->getRedirectLoginHelper();
$permissions = ['pages_messaging', 'instagram_business_basic', 'instagram_business_manage_messages']; // Permission's needed
$loginUrl = $helper->getLoginUrl(FB_REDIRECT_URL, $permissions);

// Redirect user to login
header('Location: ' . htmlspecialchars($loginUrl));
exit;