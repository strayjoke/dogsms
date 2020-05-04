<?php

namespace Strayjoke\Dogsms\Config;

use ArrayAccess;

class Config implements ArrayAccess
{
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
     *
     * @since
     * @see
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
     *
     * @return void
     *
     * @since
     * @see
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
     *
     * @return void
     *
     * @since
     * @see
     */
    public function offsetUnset($offset)
    {
        if (isset($this->config[$offset])) {
            unset($this->config[$offset]);
        }
    }
}
