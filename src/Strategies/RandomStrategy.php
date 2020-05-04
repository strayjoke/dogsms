<?php

namespace Strayjoke\Dogsms\Strategies;

use Strayjoke\Dogsms\Contracts\StrategyInterface;

class RandomStrategy implements StrategyInterface
{
    public function apply(array $gateways)
    {
        uasort($gateways, function () {
            return mt_rand() - mt_rand();
        });

        return array_values($gateways);
    }
}
