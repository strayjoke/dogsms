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

namespace Strayjoke\Dogsms\Contracts;

/**
 * interface StrategyInterface.
 */
interface StrategyInterface
{
    /**
     * 网关轮询策略.
     *
     * @param array $gateways
     *
     * @return array
     */
    public function apply(array $gateways);
}
