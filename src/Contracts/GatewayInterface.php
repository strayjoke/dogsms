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
 * interface GatewayInterface.
 */
interface GatewayInterface
{
    /**
     * 发送短信接口.
     *
     * @param [string] $phone        手机号
     * @param [string] $templateCode 模板CODE
     * @param [array]  $params       模板参数
     */
    public function sendSms($phone, $templateCode, array $params);
}
