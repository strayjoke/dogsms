<?php

namespace Strayjoke\Dogsms\Contracts;

interface StrategyInterface
{
    /**
     * 网关轮询策略
     *
     * @param array $gateways
     * @return array
     */
    public function apply(array $gateways);
}
