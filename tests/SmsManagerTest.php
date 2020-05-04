<?php

namespace Strayjoke\Dogsms\Tests;

use PHPUnit\Framework\TestCase;
use Strayjoke\Dogsms\Gateways\AlibabaCloudGateway;
use Strayjoke\Dogsms\SmsManager;

class SmsManagerTest extends TestCase
{
    public function testSortGateways()
    {
        $manager = new SmsManager(['default' => ['gateways' => ['alibaba', 'tencent']]]);
        $tempArr = $manager->sortGateways();
        $this->assertCount(2, $tempArr);
        $this->assertCount(2, $tempArr);
        $this->assertContains('alibaba', $tempArr);
        $this->assertContains('tencent', $tempArr);
    }

    public function testGateway()
    {
        $manager = new SmsManager([
            'default' => [
                'gateways' => ['alibabaCloud'],
            ],
            'gateways' => [
                'alibabaCloud' => [
                    'access_key_id'     => 'mock-id',
                    'access_key_secret' => 'mock-secret',
                    'sign_name'         => 'mock-sign',
                ],
            ],
        ]);
        $tempGateway = $manager->gateway('alibabaCloud');
        $this->assertInstanceOf(AlibabaCloudGateway::class, $tempGateway);
    }
}
