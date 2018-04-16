<?php
/**
 * Created by PhpStorm.
 * User: adrianadamiec
 * Date: 28.04.2017
 * Time: 14:45
 */

namespace Shoplo;

interface ShoploStoreAdapterInterface
{
    public function get($url, $parameters = [], $headers = []);

    public function post($url, $data, $headers = []);

    public function put($url, $data, $headers = []);

    public function delete($url);

    public function setAccessToken($accessToken);

    public function setSSOAppId($ssoAppId);

    public function fetch($uri, $body, $method, $headers = []);
}
