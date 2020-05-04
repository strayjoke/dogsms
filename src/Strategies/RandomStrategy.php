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

namespace Strayjoke\Dogsms\Strategies;

use Strayjoke\Dogsms\Contracts\StrategyInterface;

/**
 * class RandomStrategy.
 */
class RandomStrategy implements StrategyInterface
{
    /**
     * 执行方法.
     *
     * @param array $gateways
     *
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
