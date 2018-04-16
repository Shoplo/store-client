<?php
session_start();
require_once __DIR__.'/vendor/autoload.php';

ini_set('display_errors', 'TRUE');
error_reporting(E_ALL);

define('SECRET_KEY', 'xxx');
define('PUBLIC_KEY', 'yyy');

define('CALLBACK_URL', 'http://127.0.0.1/store-client/example.php');

$accessToken = $refreshToken = null;

$config = [
    'apiBaseUrl' => 'http://api.shoplo.io',
    'authBaseUrl' => 'http://auth.shoplo.io',
    'publicKey' => PUBLIC_KEY,
    'secretKey' => SECRET_KEY,
    'callbackUrl' => CALLBACK_URL,
    'accessToken' => $accessToken,
    'refreshToken' => $refreshToken,
];

$shoploApi = new Shoplo\ShoploApi($config);
$shoploApi->initSSOAuthClient($config);

if (!$shoploApi->authorized) {
    if (isset($_GET['code']) && isset($_GET['app_id'])) {
        $result = $shoploApi->accessToken($_GET['code']);
        $token = $result['access_token'];
        $appId = $_GET['app_id'];
        $shoploApi->initClient($token, $appId);

        try
        {
            $productInfo = array(
                'title'             =>  'Penne Rigate makaron pióra 500g',
                'description'       =>  'Najwyższej jakości makaron wyprodukowany w 100% z semoliny z pszenicy durum. Najlepiej smakuje z sosem pomidorowym z boczkiem, mięsem lub rybami.',
                'short_description' =>  '',
                'require_shipping'  =>  1,
                'availability'      =>  1,
                'visibility'        =>  1,
                'sku'               =>  'PR-500-P',
                'weight'            =>  50,
                'width'             =>  0,
                'height'            =>  0,
                'depth'             =>  0,
                'diameter'          =>  0,
                'buy_if_empty'      =>  0,
                'quantity'          =>  1,
                'price'             =>  619,
                'price_regular'     =>  619,
                'tax'               =>  23,
                'images'            =>  array(
                    array(
                        'src'       => 'http://lorempixel.com/640/480/food/',
                        'title'     => '',
                        'img_main'  => true
                    )
                ),
                'vendor'            =>  'Barilla',
                'category'          =>  array( 'Makarony', 'Delikatesy' ),
                'collection'        =>  array('Zdrowa żywność'),
                'tags'              =>  'penne,makaron,zdrowy'
            );
            $shoploApi->product->modify(96, ['title' => 'penne2']);
//            $data = $shoploApi->product->remove(95);
            $data = $shoploApi->product->retrieve(96);
            echo '<pre>';
            print_r($data);
            echo '</pre>';
            exit;
        }
        catch ( \Exception $e )
        {
            echo '<pre>';
            print_r($e->getMessage());
            echo '</pre>';
            exit;
        }
    } else {
        $response = $shoploApi->requestToken();
        header('Location: '.$response['login_url']);
        exit();
    }
}

exit;
