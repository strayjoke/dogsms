<?php

namespace Strayjoke\Dogsms\Gateways;

use Strayjoke\Dogsms\Contracts\GatewayInterface;
use Strayjoke\Dogsms\Exceptions\GatewayErrorException;
use Strayjoke\Dogsms\Traits\HasHttpRequest;

class AlibabaCloudGateway implements GatewayInterface
{
    use HasHttpRequest;

    private $accessKeyId;
    private $accessKeySecret;
    private $signName;
    private $scheme = 'http';
    private $host = 'dysmsapi.aliyuncs.com';
    private $regionId = 'cn-hangzhou';
    private $signatureMethod = 'HMAC-SHA1';
    private $dateTimeFormat = "Y-m-d\TH:i:s\Z";
    private $signatureVersion = '1.0';
    private $product = 'Dysmsapi';
    private $version = '2017-05-25';
    private $method = 'POST';
    private $action;
    private $connectTimeout = 5;
    private $timeout = 10;
    private $verify = false;
    private $options = [];
    private $format = 'JSON';

    public function __construct(array $config)
    {
        $this->options = [
            'http_errors'     => false,
            'connect_timeout' => $this->connectTimeout,
            'timeout'         => $this->timeout,
            'verify'          => $this->verify,
            'headers'         => [],
        ];
        $this->accessKeyId = $config['access_key_id'];
        $this->accessKeySecret = $config['access_key_secret'];
        $this->signName = $config['sign_name'];
    }

    public function sendSms($phone, $templateCode, array $params)
    {
        $this->setOptions($phone, $templateCode, $params);

        $endpoint = $this->scheme.'://'.$this->host;

        try {
            $response = $this->request($this->method, $endpoint, $this->options);
            if ($response->getStatusCode() !== 200) {
                throw new GatewayErrorException($response->getReasonPhrase(), $response->getBody());
            }

            $tmpRes = json_decode($response->getBody(), true);
            if ($tmpRes['Code'] !== 'OK') {
                throw new GatewayErrorException($tmpRes['Message']);
            }

            return $tmpRes;
        } catch (\Exception $e) {
            throw new GatewayErrorException($e->getMessage());
        }
    }

    public function signature()
    {
        $string = $this->rpcString($this->method, $this->options['form_params']);

        return base64_encode(hash_hmac('sha1', $string, $this->accessKeySecret.'&', true));
    }

    public function setOptions($phone, $templateCode, $params)
    {
        $this->options['form_params']['RegionId'] = $this->regionId;
        $this->options['form_params']['Format'] = $this->format;
        $this->options['form_params']['SignatureMethod'] = $this->signatureMethod;
        $this->options['form_params']['SignatureVersion'] = $this->signatureVersion;
        $this->options['form_params']['SignatureNonce'] = md5($this->product.$this->regionId.uniqid(md5(microtime(true)), true));
        $this->options['form_params']['Timestamp'] = gmdate($this->dateTimeFormat);
        $this->options['form_params']['Action'] = $this->setAction()->getAction();
        $this->options['form_params']['AccessKeyId'] = $this->accessKeyId;
        $this->options['form_params']['Version'] = $this->version;
        $this->options['form_params']['PhoneNumbers'] = $phone;
        $this->options['form_params']['SignName'] = $this->signName;
        $this->options['form_params']['TemplateCode'] = $templateCode;
        $this->options['form_params']['TemplateParam'] = json_encode($params);

        $this->options['form_params']['Signature'] = $this->signature();

        if (isset($this->options['form_params'])) {
            $this->options['form_params'] = \GuzzleHttp\Psr7\parse_query(
                $this->alibabacloudToString($this->options['form_params'])
            );
        }
    }

    public function setAction($action = 'SendSms')
    {
        $this->action = $action;

        return $this;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function alibabacloudToString($data)
    {
        $string = '';
        foreach ($data as $key => $value) {
            $encode = rawurlencode($value);
            $string .= "$key=$encode&";
        }

        if (0 < count($data)) {
            $string = substr($string, 0, -1);
        }

        return $string;
    }

    private function rpcString($method, $params)
    {
        ksort($params);
        $canonicalized = '';
        foreach ($params as $key => $value) {
            $canonicalized .= '&'.$this->percentEncode($key).'='.$this->percentEncode($value);
        }

        return $method.'&%2F&'.$this->percentEncode(substr($canonicalized, 1));
    }

    private function percentEncode($string)
    {
        $result = urlencode($string);
        $result = str_replace(['+', '*'], ['%20', '%2A'], $result);
        $result = preg_replace('/%7E/', '~', $result);

        return $result;
    }
}
