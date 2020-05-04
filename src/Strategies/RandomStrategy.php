<?php

namespace Strayjoke\Dogsms\Strategies;

use Strayjoke\Dogsms\Contracts\StrategyInterface;

/**
 * class RandomStrategy
 */
class RandomStrategy implements StrategyInterface
{
    /**
     * 执行方法
     *
     * @param array $gateways
     * @return array
     */
    public function apply(array $gateways)
    {
        uasort($gateways, function () {
            return mt_rand() - mt_rand();
        });

        return array_values($gateways);
    }
}
