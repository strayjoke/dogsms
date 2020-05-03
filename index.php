<?php

require __DIR__ . '/vendor/autoload.php';

use Strayjoke\Dogsms\Gateways\TencentCloudGateway;

$sms = new TencentCloudGateway([
    'secret_id' => 'AKIDKgcHutU1B5jDkGvrYzN3QTMzDAZJMm8L',
    'secret_key' => 'us6lHOUVlt87pGWQ7XfXGPuQhqOfm7C0',
    'sign_name' => '风了网',
]);


print_r('start');
$response = $sms->sendSms('17717935765', '566994', ['code' => '1234']);
print_r(json_decode($response->getBody(), true)["Response"]);

print_r('end');
