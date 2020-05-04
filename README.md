<h1 align="center"> dogsms </h1>

<p align="center"> 发送短信的php开发包, 支持阿里云短信和腾讯云短信</p>


## 安装

```
$ composer require strayjoke/dogsms -vvv
```

## 用法
目前只支持国内短信(+86),不支持群发短信。

#### 配置
```
$config =[
    // 默认发送配置
    'default' => [
        // 默认可用的发送网关
        'gateways' => [
            'alibabaCloud', 'tencentCloud',
        ],
    ],
    // 可用的网关配置
    'gateways' => [
        'tencentCloud' => [
            'secret_id' => 'AKIDTijUzFxxxxxxxxxxxxx', //密钥id
            'secret_key' => 'QcdXBXcT5xxxxxxxxxxxxx', //密钥
            'sign_name' => 'xxxx',               //短信签名
        ],
        'alibabaCloud' => [
            'access_key_id' => 'LTAIMjVxxxxxxxx',     //密钥id
            'access_key_secret' => 'NnE90xxxxxxxxx',   //密钥
            'sign_name' => 'xxxxx',     //短信签名
        ]
    ]
];
```

#### 调用方法
```
use Strayjoke\Dogsms\Dogsms;

$sms = new Dogsms($config);

$sms->sendSms(17533333333, 
    [
        'alibabaCloud' => 'aliCode',      //短信模板编号， `alibabacloud` 对应配置文件里的 `alibabacloud`
        'tencentCloud' => 'tencentCode'   //短信模板编号， `tencentCloud` 对应配置文件里的 `tencentCloud`
    ], 
    [
        'code' =>1234 //短信模板的参数，阿里云和腾讯云公用参数。其中阿里云短信需要提供数组key， 腾讯云不需要提供。
    ] 
);
```

## License

MIT