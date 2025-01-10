<?php

require_once 'vendor/autoload.php';

$client = new Google\Client();

$client->setApplicationName("google-drive");
$client->setDeveloperKey("YOUR_APP_KEY");

$client->setAuthConfig('client_secret.json');

$client->addScope(Google\Service\Drive::DRIVE);

$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/GoogleDrive/access_token.php';
$client->setRedirectUri($redirect_uri);
$client->setAccessType('offline');

if (!isset($_GET['code']) && !isset($_COOKIE['Google-Drive'])) {
    $auth_url = $client->createAuthUrl();
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
    header('location: access_token.php');
}

?>