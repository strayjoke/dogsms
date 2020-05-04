<?php

namespace Strayjoke\Dogsms\Contracts;

interface GatewayInterface
{
    /**
     * 发送短信接口.
     *
     * @param [string] $phone        手机号
     * @param [string] $templateCode 模板CODE
     * @param [array]  $params       模板参数
     *
     * @return void
     */
    public function sendSms($phone, $templateCode, array $params);
}
