<?php

namespace Strayjoke\Dogsms;

use Closure;

class Dogsms
{
    const STATUS_SUCCESS = 'success';

    const STATUS_FAILURE = 'failure';

    private $manager;

    public function __construct(array $config = [])
    {
        $this->manager = new SmsManager($config);
    }

    //发送短信
    public function sendSms($phone, $templateCode, $params)
    {
        $results = [];
        $manager = $this->manager;
        $gateways = $manager->sortGateways();
        foreach ($gateways as $name) {
            try {
                $results[$name] = [
                    'gateway' => $name,
                    'status' => self::STATUS_SUCCESS,
                    'result' => $manager->gateway($name)->sendSms($phone, $templateCode, $params)
                ];
                break;
            } catch (\Exception $e) {
                $results[$name] = [
                    'gateway' => $name,
                    'status' => self::STATUS_FAILURE,
                    'exception' => $e,
                ];
            } catch (\Throwable $e) {
                $results[$name] = [
                    'gateway' => $name,
                    'status' => self::STATUS_FAILURE,
                    'exception' => $e,
                ];
            }
            return $results;
        }
    }

    //自定义扩展
    public function extend($name, Closure $callback)
    {
        $this->manager->extend($name, $callback);
    }
}
