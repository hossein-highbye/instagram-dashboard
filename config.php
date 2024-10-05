<?php

// Database conf
define('DB_HOST', 'localhost');
define('DB_NAME', 'instagram_4berry');
define('DB_USER', '4berryIncINs');
define('DB_PASS', '*C^5Cf8Ly3ccj1FZ#I');

// facebook app cred
define('FB_APP_ID', '1096662008567613');
define('FB_APP_SECRET', 'bc8b0c441eb30f011ccb675da33a7740');
define('FB_REDIRECT_URL', 'your_redirect_url');

$url = 'https://graph.facebook.com/oauth/access_token?client_id={$appid}&client_secret={$appsecret}&grant_type=client_credentials';
