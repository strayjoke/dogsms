<?php

namespace Strayjoke\Dogsms;

use Closure;
use Strayjoke\Dogsms\Contracts\GatewayInterface;
use Strayjoke\Dogsms\Contracts\StrategyInterface;
use Strayjoke\Dogsms\Strategies\RandomStrategy;
use Strayjoke\Dogsms\Exceptions\InvalidArgumentException;
use Strayjoke\Dogsms\Config\Config;

class SmsManager
{
    //配置
    private $config;
    //策略
    private $strategy;
    //网关
    private $gateways = [];
    //自定义扩展
    private $customCreators = [];

    public function __construct(array $config = [])
    {
        $this->config = new Config($config);
        $this->gateways = $this->config->get('default.gateways', []);
    }

    /**
     * 自定义扩展短信驱动
     *
     * @param string $name
     * @param Closure $callback
     * @return $this
     */
    public function extend($name, Closure $callback)
    {
        $this->customCreators[$name] = $callback;
        return $this;
    }

    public function gateway($name)
    {
        if (!isset($this->gateways[$name])) {
            $this->gateways[$name] = $this->createGateway($name);
        }

        return $this->gateways[$name];
    }

    /**
     * 获取策略实例
     *
     * @param [string] $strategy
     * @return Strayjoke\Dogsms\Contracts\StrategyInterface
     */
    public function strategy($strategy = null)
    {
        if (is_null($strategy)) {
            $strategy = $this->config->get('default.strategy', RandomStrategy::class);
        }

        if (!\class_exists($strategy)) {
            $strategy = __NAMESPACE__ . '\Strategies\\' . ucfirst($strategy) . 'php';
        }

        if (!class_exists($strategy)) {
            throw new  InvalidArgumentException("$strategy 策略不存在");
        }

        if (empty($this->strategy) || !($this->strategy instanceof StrategyInterface)) {
            $this->strategy = new $strategy($this);
        }

        return $this->strategy;
    }

    public function createGateway($name)
    {
        //自定义扩展里读取
        if (isset($this->customCreators[$name])) {
            $gateway = $this->callCustomCreator($name);
        } else {
            $className = $this->formatGatewayClassName($name);
            //自定义配置里读取
            $config = $this->config->get("gateways.{$name}", []);
            $gateway = $this->makeGateway($className, $config);
        }

        if (!$gateway instanceof GatewayInterface) {
            throw new InvalidArgumentException(\sprintf('Gateway "%s" must implement interface %s.', $name, GatewayInterface::class));
        }

        return $gateway;
    }

    public function callCustomCreator($name)
    {
        return \call_user_func($this->customCreators[$name], $this->config->get("gateways.{$name}", []));
    }

    public function formatGatewayClassName($name)
    {
        if (\class_exists($name) && \in_array(GatewayInterface::class, \class_implements($name))) {
            return $name;
        }

        $name = \ucfirst(\str_replace(['-', '_', ''], '', $name));

        return __NAMESPACE__ . "\\Gateways\\{$name}Gateway";
    }

    public function makeGateway($className, $config)
    {
        if (!\class_exists($className) && !\in_array(GatewayInterface::class, \class_implements($className))) {
            throw new InvalidArgumentException(\sprintf('Class "%s" is a invalid dogsms gateway.', $className));
        }

        return new $className($config);
    }

    public function sortGateways()
    {
        $strategy = $this->strategy;
        if (empty($strategy)) {
            $strategy = $this->strategy();
        }

        return $strategy->apply($this->gateways);
    }
}
