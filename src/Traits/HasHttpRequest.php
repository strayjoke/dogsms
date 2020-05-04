<?php

namespace Strayjoke\Dogsms\Traits;

use GuzzleHttp\Client;

trait HasHttpRequest
{
    //请求
    private function request($method, $uri, $options)
    {
        $client = new Client(['base_uri' => '',  'timeout' => 5.0]);

        return $client->request($method, $uri, $options);
    }

    //发起 post 请求
    private function post($uri, $options)
    {
        $client = new Client(['base_uri' => '',  'timeout' => 30]);

        return $client->post($uri, $options);
    }
}
