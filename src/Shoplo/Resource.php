<?php

namespace Shoplo;

use Shoplo\ShoploStoreAdapterInterface;

class Resource
{
    protected $client;
    protected $bucket = array();
    protected $prefix = '';
    protected $api_url;

    /**
     * Resource constructor.
     * @param ShoploStoreAdapterInterface $client
     * @param $apiUrl
     */
    public function __construct(ShoploStoreAdapterInterface $client, $apiUrl)
    {
        $this->client = $client;
        $this->api_url = $apiUrl;
    }

    protected function prepare_params($params)
    {
        $string = '';
        if (is_array($params)) {
            foreach ($params as $k => $v) {
                if (!is_array($v)) {
                    $string .= $k.'='.urlencode($v).'&';
                }
            }
            $string = substr($string, 0, strlen($string) - 1);
        }

        return $string;
    }

    protected function prepare_result($result)
    {
        if (!is_array($result)) {
            return array();
        }

        if (isset($result['status'])) {
            return $result;
        }

        return $result;
    }

    protected function send($uri, $request = 'GET', $fields = array())
    {
        if (0 === strpos($uri, '/')) {
            $uri = substr($uri, 1);
        }
        $uri = '/services/'.$uri;

        $method = $request;

        try {
            if (!$this->client instanceof ShoploStoreAdapterInterface) {
                throw new ShoploException("No authorisation");
            }

            $result = $this->client->fetch($this->api_url.$uri, $fields, $method);
        } catch (\Exception $e) {
            throw new AuthException($e->getMessage());
        }

        if (isset($result['status']) && $result['status'] == 'err') {
            if ($result['error'] == '202') #Authorize error - need generate new access token
            {
                throw new AuthException($result['error_msg']);
            }elseif ($result['error'] == '251') #Authorize error - need generate new access token
            {
                throw new TokenExpiredException($result['error_msg']);
            }
            throw new ShoploException($result['error_msg']);
        }

        return $result;
    }

    public function __destruct()
    {
        unset($this->client);
        unset($this->bucket);
    }
}