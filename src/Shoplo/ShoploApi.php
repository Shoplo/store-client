<?php

namespace Shoplo;

use Shoplo\Guzzle\GuzzleAdapter;
use SSOAuth\SSOAuthClient;

define('SHOPLO_REQUEST_TOKEN_URI', '/services/oauth/request_token');
define('SHOPLO_ACCESS_TOKEN_URI', '/services/oauth/access_token');

class ShoploApi
{
    /**
     * @var String
     */
    private $api_key;

    /**
     * @var String
     */
    private $secret_key;

    /**
     * @var ShoploAuthStore
     */
    private $auth_store;

    /**
     * @var String
     */
    private $oauth_token;

    /**
     * @var String
     */
    private $oauth_token_secret;

    /**
     * @var Boolean
     */
    public $authorized = false;

    /**
     * @var Assets
     */
    public $assets;

    /**
     * @var Cart
     */
    public $cart;

    /**
     * @var Category
     */
    public $category;

    /**
     * @var Collection
     */
    public $collection;

    /**
     * @var Vendor
     */
    public $vendor;

    /**
     * @var Customer
     */
    public $customer;

    /**
     * @var Order
     */
    public $order;

    /**
     * @var OrderStatus
     */
    public $order_status;

    /**
     * @var Product
     */
    public $product;

    /**
     * @var ProductImage
     */
    public $product_image;

    /**
     * @var ProductVariant
     */
    public $product_variant;

    /**
     * @var Shop
     */
    public $shop;

    /**
     * @var Theme
     */
    public $theme;

    /**
     * @var User
     */
    public $user;

    /**
     * @var Webhook
     */
    public $webhook;

    /**
     * @var Payment
     */
    public $payment;

    /**
     * @var Page
     */
    public $page;

    /**
     * @var Shipping
     */
    public $shipping;

    /**
     * @var Checkout
     */
    public $checkout;

    /**
     * @var Voucher
     */
    public $voucher;

    /**
     * @var Promotion
     */
    public $promotion;

    /**
     * @var ApplicationCharge
     */
    public $application_charge;

    /**
     * @var RecurringApplicationCharge
     */
    public $recurring_application_charge;

    /**
     * @var Transaction
     */
    public $transaction;

    public $api_url;

    /**
     * @var String
     */
    public $shop_domain = null;

    /**
     * @var SSOAuthClient
     */
    private $ssoAuthClient;

    private $shoploStoreAdapterInterface;

    public function __construct($config, $authStore = null, $disableSession = false)
    {
        if (!$disableSession && !session_id()) {
            throw new ShoploException('Session not initialized');
        }
        if (empty($config['publicKey'])) {
            throw new ShoploException('Invalid Api Key');
        } elseif (empty($config['secretKey'])) {
            throw new ShoploException('Invalid Api Key');
        } elseif (empty($config['callbackUrl'])) {
            throw new ShoploException('Invalid Callback Url');
        }

        $this->api_url = !empty($config['apiBaseUrl']) ? $config['apiBaseUrl'] : 'http://api.shoplo.com';

        $this->api_key = $config['publicKey'];
        $this->secret_key = $config['secretKey'];

        if (isset($_GET['shop_domain'])) {
            $this->shop_domain = addslashes($_GET['shop_domain']);
        }

        $this->callback_url = (false === strpos(
                $config['callbackUrl'],
                'http'
            )) ? 'http://'.$config['callbackUrl'] : $config['callbackUrl'];

        $this->auth_store = AuthStore::getInstance($authStore);

        $this->shoploStoreAdapterInterface = new GuzzleAdapter(
            new \GuzzleHttp\Client(['base_uri' => $this->api_url])
        );

        $this->initSSOAuthClient($config);
    }

    /**
     * @param $config
     */
    public function initSSOAuthClient($config)
    {
        $guzzleAuthConfig = [
            'base_uri' => $config['authBaseUrl'],
        ];

        $guzzleAdapter = new \SSOAuth\Guzzle\GuzzleAdapter(
            new \GuzzleHttp\Client($guzzleAuthConfig)
        );
        $this->ssoAuthClient = new \SSOAuth\SSOAuthClient(
            $guzzleAdapter,
            $config
        );
    }

    public function getAuthorizeUrl()
    {
        return $this->api_url.'/services/oauth/authorize';
    }

    public function initClient($token = null, $appId = null)
    {
        $client = $this->getClient($token, $appId);

        $this->assets = new Assets($client, $this->api_url);
        $this->category = new Category($client, $this->api_url);
        $this->cart = new Cart($client, $this->api_url);
        $this->collection = new Collection($client, $this->api_url);
        $this->customer = new Customer($client, $this->api_url);
        $this->order = new Order($client, $this->api_url);
        $this->order_status = new OrderStatus($client, $this->api_url);
        $this->product = new Product($client, $this->api_url);
        $this->product_image = new ProductImage($client, $this->api_url);
        $this->product_variant = new ProductVariant($client, $this->api_url);
        $this->vendor = new Vendor($client, $this->api_url);
        $this->shop = new Shop($client, $this->api_url);
        $this->webhook = new Webhook($client, $this->api_url);
        $this->theme = new Theme($client, $this->api_url);
        $this->payment = new Payment($client, $this->api_url);
        $this->page = new Page($client, $this->api_url);
        $this->shipping = new Shipping($client, $this->api_url);
        $this->checkout = new Checkout($client, $this->api_url);
        $this->voucher = new Voucher($client, $this->api_url);
        $this->promotion = new Promotion($client, $this->api_url);
        $this->user = new User($client, $this->api_url);
        $this->application_charge = new ApplicationCharge($client, $this->api_url);
        $this->recurring_application_charge = new RecurringApplicationCharge($client, $this->api_url);
        $this->transaction = new Transaction($client, $this->api_url);
    }

    public function authorize($token, $tokenSecret)
    {
        if ($this->auth_store->authorize($token, $tokenSecret)) {
            $this->oauth_token = $this->auth_store->getOAuthToken();
            $this->oauth_token_secret = $this->auth_store->getOAuthTokenSecret();
            $this->authorized = true;

            return true;
        }

        $this->authorized = false;

        return false;
    }

    public function requestToken()
    {
        $response['login_url'] = $this->ssoAuthClient->requestToken(true);

        return $response;
    }

    public function accessToken($code)
    {
        $response = $this->ssoAuthClient->getAccessToken($code);
        $this->auth_store->setAuthorizeData($response['access_token'], $response['refresh_token']);

        $this->oauth_token = $response['access_token'];
        $this->oauth_token_secret = $response['refresh_token'];

        $this->shoploStoreAdapterInterface->setSSOAppId($_GET['app_id']);
        $this->shoploStoreAdapterInterface->setAccessToken(
            $response['access_token']
        );

        return $response;
    }

    /**
     * @param null $token
     * @param null $appId
     * @return \Shoplo\Guzzle\GuzzleAdapter
     */
    public function getClient($token = null, $appId = null)
    {
        if ($token) {
            $this->shoploStoreAdapterInterface->setSSOAppId($appId);
            $this->shoploStoreAdapterInterface->setAccessToken($token);
        }

        return $this->shoploStoreAdapterInterface;
    }

    public function getOAuthToken()
    {
        return $this->oauth_token;
    }

    public function getOAuthTokenSecret()
    {
        return $this->oauth_token_secret;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->api_key;
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secret_key;
    }

    public function __destruct()
    {
        unset($this->api_key);
        unset($this->secret_key);
        unset($this->oauth_token);
        unset($this->oauth_token_secret);
        unset($this->category);
        unset($this->cart);
        unset($this->collection);
        unset($this->customer);
        unset($this->order);
        unset($this->product);
        unset($this->product_image);
        unset($this->product_variant);
        unset($this->vendor);
        unset($this->shop);
        unset($this->theme);
        unset($this->user);
        unset($this->webhook);
        unset($this->application_charge);
        unset($this->recurring_application_charge);
        unset($this->transaction);
    }
}
