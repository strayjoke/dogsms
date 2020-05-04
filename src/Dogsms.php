<?php

namespace Strayjoke\Dogsms;

use Closure;

/**
 * class Dogsms
 *
 */
class Dogsms
{
    const STATUS_SUCCESS = 'success';

    const STATUS_FAILURE = 'failure';

    private $manager;

    /**
     * 构造函数
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->manager = new SmsManager($config);
    }

    /**
     * 发送短信
     *
     * @param [string] $phone
     * @param [array] $templateCode
     * @param [array] $params
     * @return array
     */
    public function sendSms($phone, $templateCode, $params)
    {
        $results = [];
        $manager = $this->manager;
        $gateways = $manager->sortGateways();
        foreach ($gateways as $name) {
            try {
                $results[$name] = [
                    'gateway' => $name,
                    'status'  => self::STATUS_SUCCESS,
                    'result'  => $manager->gateway($name)->sendSms($phone, $templateCode[$name], $params),
                ];
                break;
            } catch (\Exception $e) {
                $results[$name] = [
                    'gateway'   => $name,
                    'status'    => self::STATUS_FAILURE,
                    'exception' => $e,
                ];
            } catch (\Throwable $e) {
                $results[$name] = [
                    'gateway'   => $name,
                    'status'    => self::STATUS_FAILURE,
                    'exception' => $e,
                ];
            }
        }

        return $results;
    }

    /**
     * 扩展
     *
     * @param [string] $name
     * @param Closure $callback
     * @return $this
     */
    public function extend($name, Closure $callback)
    {
        $this->manager->extend($name, $callback);
    }
}
