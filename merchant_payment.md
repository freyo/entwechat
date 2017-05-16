title: 企业支付
---

在接口使用上，企业号与服务号、订阅号则有所区别，请开发者注意：

1. 由于企业号标识内部成员使用的是userid，因此在使用微信支付下单时，需要调用 （userid转换openid接口），将userid转换成openid进行支付。在调用支付下单接口时，APPID请使用企业号的corpid。

2. 如需让企业号成员外的用户进行微信支付，请使用企业号的 （OAuth2验证接口） ，若获取不到userid，即当前微信用户不在企业号内，则会返回该用户对应此企业号的openid。

你在阅读本文之前确认你已经仔细阅读了：[微信支付 | 企业付款文档 ](https://pay.weixin.qq.com/wiki/doc/api/tools/mch_pay.php?chapter=14_1)。

## 配置

与其他支付接口一样，企业支付接口也需要配置如下参数，需要特别注意的是，企业支付相关的全部接口 **都需要使用 SSL 证书**，因此 **cert_path 以及 cert_key 必须正确配置**。

```php
<?php

use EntWeChat\Foundation\Application;

$options = [
    // payment
    'payment' => [
        'merchant_id'        => 'your-mch-id',
        'key'                => 'key-for-signature',
        'cert_path'          => 'path/to/your/cert.pem',
        'key_path'           => 'path/to/your/key',
        // ...
    ],
];

$app = new Application($options);

$merchantPay = $app->merchant_pay;
```

## 企业付款

企业付款使用的余额跟微信支付的收款并非同一账户，请注意充值。

### 发送接口

```php
<?php

$merchantPayData = [
        'partner_trade_no' => str_random(16), //随机字符串作为订单号，跟红包和支付一个概念。
        'openid' => $openid, //收款人的openid
        'check_name' => 'NO_CHECK',  //文档中有三种校验实名的方法 NO_CHECK OPTION_CHECK FORCE_CHECK
        're_user_name'=>'张三',     //OPTION_CHECK FORCE_CHECK 校验实名的时候必须提交
        'amount' => 100,  //单位为分
        'desc' => '企业付款',
        'spbill_create_ip' => '192.168.0.1',  //发起交易的IP地址
    ];
$result = $merchantPay->send($merchantPayData);

```

> 更多参数请参考官方文档中参数列表。

## 查询付款信息

用于商户对已发放的企业支付进行查询企业支付的具体信息。

```php
$partnerTradeNo = "商户系统内部的订单号（partner_trade_no）";
$merchantPay->query($partnerTradeNo);
```
