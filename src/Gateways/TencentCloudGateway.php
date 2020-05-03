<?php

namespace Strayjoke\Dogsms\Gateways;

use Strayjoke\Dogsms\Contracts\GatewayInterface;
use Strayjoke\Dogsms\Traits\HasHttpRequest;
use Strayjoke\Dogsms\Exceptions\GatewayErrorException;

class TencentCloudGateway implements GatewayInterface
{
    use HasHttpRequest;

    //请求方法
    const METHOD = 'POST';
    const HOST = 'sms.tencentcloudapi.com';
    //应用id
    private $smsSdkAppId = '1400342134';
    private $region = 'ap-shanghai';
    private $action;
    private $version = '2019-07-11';
    private $secretId;
    private $secretKey;
    private $scheme = 'https';
    private $signName;
    private $connectTimeout = 5;
    private $timeout = 10;
    private $verify = false;
    private $options = [];

    public function __construct(array $config)
    {
        $this->options = [
            'http_errors' => false,
            'connect_timeout' => $this->connectTimeout,
            'timeout' => $this->timeout,
            'verify' => $this->verify,
            'headers' => []
        ];
        $this->secretId = $config['secret_id'];
        $this->secretKey = $config['secret_key'];
        $this->signName = $config['sign_name'];
    }

    //发送短信
    public function sendSms($phone, $templateCode, $params)
    {
        $this->setOptions($phone, $templateCode, $params);

        $endpoint = $this->scheme . '://' . $this->host;

        try {
            $response = $this->post($endpoint, $this->options);
            if ($response->getStatusCode() !== 200) {
                throw new GatewayErrorException($response->getReasonPhrase(), $response->getBody());
            }
            $tmpRes = json_decode($response->getBody(), true)["Response"];
            if (array_key_exists("Error", $tmpRes)) {
                throw new GatewayErrorException($tmpRes["Error"]["Message"], $tmpRes["Error"]["Code"]);
            }
        } catch (\Exception $e) {
            throw new GatewayErrorException($e->getMessage());
        }
    }

    public function setOptions($phone, $templateCode, $params)
    {
        $this->options['headers']['Host'] = self::HOST;
        $this->options['headers']['X-TC-Action'] = $this->setAction()->getAction();
        $this->options['headers']['X-TC-Version'] = $this->version;
        $this->options['headers']['X-TC-Region'] = $this->region;
        $this->options['headers']['X-TC-Timestamp'] = time();
        $this->options['headers']['Content-Type'] = "application/json";
        $this->options['form_params']['PhoneNumberSet'] = ['+86' . $phone];
        $this->options['form_params']['TemplateID'] = $templateCode;
        $this->options['form_params']['SmsSdkAppid'] = $this->smsSdkAppId;
        $this->options['form_params']['Sign'] = $this->signName;
        $this->options['form_params']['TemplateParamSet'] = array_values($params);
        $this->options['form_params']['ExtendCode'] = "0";
        $this->options['form_params']['SessionContext'] = "";
        $this->options['form_params']['SenderId'] = "";

        $payload = json_encode($this->options['form_params'], JSON_UNESCAPED_UNICODE);
        $this->options['headers']['Authorization'] = $this->signature($payload);
        $this->options['body'] = $payload;
        unset($this->options['form_params']);
    }

    //计算签名 TC3-HMAC-SHA256
    public function signature($payload)
    {
        $canonicalURI = '/';  //uri参数
        $canonicalQueryString = ""; //查询字符串
        $payloadHash = hash("SHA256", $payload);
        $canonicalHeaders = "content-type:" . $this->options['headers']['Content-Type'] . "\n" .
            "host:" . $this->options['headers']['Host'] . "\n";
        $signedHeaders = "content-type;host";

        $canonicalRequest = SELF::METHOD . "\n" .
            $canonicalURI . "\n" .
            $canonicalQueryString . "\n" .
            $canonicalHeaders . "\n" .
            $signedHeaders . "\n" .
            $payloadHash;
        $algo = "TC3-HMAC-SHA256";
        $date = gmdate("Y-m-d", $this->options['headers']['X-TC-Timestamp']);
        $service = explode(".", $this->options['headers']['Host'])[0];
        $credentialScope = $date . "/" . $service . "/tc3_request";
        $hashedCanonicalRequest = hash("SHA256", $canonicalRequest);
        $str2sign = $algo . "\n" .
            $this->options['headers']['X-TC-Timestamp'] . "\n" .
            $credentialScope . "\n" .
            $hashedCanonicalRequest;
        $signature = $this->signTC3($this->secretKey, $date, $service, $str2sign);

        $auth = $algo .
            " Credential=" . $this->secretId . "/" . $credentialScope .
            ", SignedHeaders=content-type;host, Signature=" . $signature;
        return $auth;
    }

    private function signTC3($skey, $date, $service, $str2sign)
    {
        $dateKey = hash_hmac("SHA256", $date, "TC3" . $skey, true);
        $serviceKey = hash_hmac("SHA256", $service, $dateKey, true);
        $reqKey = hash_hmac("SHA256", "tc3_request", $serviceKey, true);
        return hash_hmac("SHA256", $str2sign, $reqKey);
    }

    public function setAction($action = "SendSms")
    {
        $this->action = $action;
        return $this;
    }

    public function getAction()
    {
        return $this->action;
    }
}