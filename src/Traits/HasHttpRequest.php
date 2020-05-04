<?php

/*
 * This file is part of the strayjoke/dogsms.
 *
 * (c) strayjoke <strayjoke@163.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 *
 */

namespace Strayjoke\Dogsms\Traits;

use GuzzleHttp\Client;

/**
 *  trait HasHttpRequest.
 */
trait HasHttpRequest
{
    /**
     * 请求
     *
     * @param [string] $method
     * @param [string] $uri
     * @param [array]  $options
     *
     * @return array
     */
    private function request($method, $uri, array $options)
    {
        $client = new Client(['base_uri' => '',  'timeout' => 5.0]);

        return $client->request($method, $uri, $options);
    }

    /**
     * post 请求
     *
     * @param [string] $uri
     * @param [array]  $options
     *
     * @return array
     */
    private function post($uri, $options)
    {
        $client = new Client(['base_uri' => '',  'timeout' => 30]);

        return $client->post($uri, $options);
    }
}
