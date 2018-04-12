<?php
session_start();
require_once __DIR__.'/vendor/autoload.php';

ini_set('display_errors', 'TRUE');
error_reporting(E_ALL);

define('SECRET_KEY', '89da2c8cad0f74a558bc9c47924dc9e6');
define('PUBLIC_KEY', '95897d01c802ed5ceae1348bb0efae7a');

define('CALLBACK_URL', 'http://127.0.0.1/store-client/example.php');

$accessToken = $refreshToken = null;

$config = [
    'apiBaseUrl'   => 'http://auth.s6.test.shoplo.io',
    'publicKey'    => PUBLIC_KEY,
    'secretKey'    => SECRET_KEY,
    'callbackUrl'  => CALLBACK_URL,
    'accessToken'  => $accessToken,
    'refreshToken' => $refreshToken,
];

$guzzleConfig = [
    'base_uri' => 'http://auth.s6.test.shoplo.io',
];

$guzzleAdapter     = new \SSOAuth\Guzzle\GuzzleAdapter(
    new \GuzzleHttp\Client($guzzleConfig)
);
$shoploMultiClient = new \SSOAuth\SSOAuthClient(
    $guzzleAdapter,
    $config
);

$response = $shoploMultiClient->authorize();
echo $shoploMultiClient->ssoAppId;
print_r($response);


//


exit;
