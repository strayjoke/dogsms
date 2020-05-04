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

namespace Strayjoke\Dogsms\Config;

use ArrayAccess;

/**
 * class Config.
 */
class Config implements ArrayAccess
{
    /**
     * @var array
     */
    private $config = [];

    /**
     * 构造函数.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * 获取配置信息.
     *
     * @param [string] $key
     * @param [mixed]  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $config = $this->config;

        if (isset($config[$key])) {
            return $config[$key];
        }

        if (false === strpos($key, '.')) {
            return $default;
        }

        foreach (explode('.', $key) as $item) {
            if (!is_array($config) || !array_key_exists($item, $config)) {
                return $default;
            }

            $config = $config[$item];
        }

        return $config;
    }

    /**
     * 实现 ArrayAccess 接口.
     *
     * @param [mixed] $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->config);
    }

    /**
     * 实现 ArrayAccess 接口.
     *
     * @param [mixed] $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * 实现 ArrayAccess 接口.
     *
     * @param [string] $offset
     * @param [mixed]  $value
     */
    public function offsetSet($offset, $value)
    {
        if (isset($this->config[$offset])) {
            $this->config[$offset] = $value;
        }
    }

    /**
     * 实现 ArrayAccess 接口.
     *
     * @param [string] $offset
     */
    public function offsetUnset($offset)
    {
        if (isset($this->config[$offset])) {
            unset($this->config[$offset]);
        }
    }
}
