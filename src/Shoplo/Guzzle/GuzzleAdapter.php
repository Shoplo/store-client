<?php

namespace Shoplo\Guzzle;

use Shoplo\ShoploStoreAdapterInterface;

class GuzzleAdapter implements ShoploStoreAdapterInterface
{
    private $accessToken;
    private $ssoAppId;
    /** @var  \GuzzleHttp\Client */
    private $guzzle;

    public function __construct(
        \GuzzleHttp\Client $guzzle,
        $accessToken = null,
        $ssoAppId = null
    ) {
        $this->guzzle = $guzzle;
        $this->accessToken = $accessToken;
        $this->ssoAppId = $ssoAppId;
    }

    /**
     * @param null $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @param null $ssoAppId
     */
    public function setSSOAppId($ssoAppId)
    {
        $this->ssoAppId = $ssoAppId;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return !$this->accessToken && $this->ssoAppId
            ? []
            : [
                'Authorization' => "Bearer {$this->accessToken}",
                'app-id' => $this->ssoAppId,
                'Content-Type' => 'application/json; charset=utf-8',
            ];
    }

    /**
     * @param       $url
     * @param array $parameters
     * @param array $headers
     *
     * @return string
     * @throws \Exception
     */
    public function get($url, $parameters = [], $headers = [])
    {
        $headers = array_merge($headers, $this->getHeaders());

        try {
            $rsp = $this->guzzle->request(
                'GET',
                $url,
                [
                    'headers' => $headers,
                ]
            );

            return json_decode($rsp->getBody()->getContents(), true);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            throw $e;
        }
    }

    /**
     * @param       $url
     * @param $data
     * @param array $headers
     *
     * @return string
     * @throws \Exception
     */
    public function put($url, $data, $headers = [])
    {
        try {
            $headers = array_merge($headers, $this->getHeaders());
            $rsp = $this->guzzle->request(
                'PUT',
                $url,
                [
                    'headers' => $headers,
                    'query' => http_build_query($data),
                ]
            );

            return $rsp->getBody()->getContents();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param       $url
     * @param $data
     * @param array $headers
     *
     * @return string
     * @throws \Exception
     */
    public function post($url, $data, $headers = [])
    {
        try {
            $headers = array_merge($headers, $this->getHeaders());
            $rsp = $this->guzzle->request(
                'POST',
                $url,
                [
                    'headers' => $headers,
                    'query' => http_build_query($data),
                ]
            );

            return $rsp->getBody()->getContents();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param       $url
     * @param array $headers
     *
     * @return mixed
     * @throws \Exception
     */
    public function delete($url, $headers = [])
    {
        try {
            $headers = array_merge($headers, $this->getHeaders());
            $rsp = $this->guzzle->delete(
                $url,
                [
                    'headers' => $headers,
                ]
            );

            $json = \GuzzleHttp\json_decode($rsp->getBody(), true);

            return $json;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $uri
     * @param $body
     * @param $method
     * @param array $headers
     * @return mixed|string
     * @throws \Exception
     */
    public function fetch($uri, $body, $method, $headers = [])
    {
        switch ($method) {
            case 'POST':
                return $this->post($uri, $body, $headers);
                break;
            case 'PUT':
                return $this->put($uri, $body, $headers);
                break;
            case 'DELETE':
                return $this->delete($uri, $headers);
                break;
            case 'GET':
            default:
                return $this->get($uri, $body, $headers);
        }
    }
}