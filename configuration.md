title: 配置
---

在前面我们已经讲过，初始化 SDK 的时候方法就是创建一个 `EntWeChat\Foundation\Application` 实例：

```php
use EntWeChat\Foundation\Application;

$options = [
   // ...
];

$app = new Application($options);

/**
* 如果想要在Application实例化完成之后, 修改某一个options的值,
* 比如服务商+子商户支付回调场景, 所有子商户订单支付信息都是通过同一个服务商的$option 配置进来的,
* 当oauth在微信端验证完成之后, 可以通过动态设置merchant_id来区分具体是哪个子商户
*/
$app['config']->set('oauth.callback','wechat/oauthcallback/'. $sub_merchant_id->id);
```

那么配置的具体选项有哪些，下面是一个完整的列表：

```php
<?php

return [
    /*
     * Debug 模式，bool 值：true/false
     *
     * 当值为 false 时，所有的日志都不会记录
     */
    'debug' => true,

    /*
     * 使用 Laravel 的缓存系统
     */
    'use_laravel_cache' => true,

    /*
     * 账号基本信息，请从微信公众平台获取
     */

    //单账号
    'corp_id' => env('WECHAT_APPID', 'your-app-id'),         // AppID
    'secret'  => env('WECHAT_SECRET', 'your-app-secret'),     // AppSecret
    'token'   => env('WECHAT_TOKEN', 'your-token'),          // Token
    'aes_key' => env('WECHAT_AES_KEY', ''),                    // EncodingAESKey
    /*
     * OAuth 配置
     *
     * only_wechat_browser: 只在微信浏览器跳转
     * scopes：snsapi_base
     * callback：OAuth授权完成后的回调页地址(如果使用中间件，则随便填写。。。)
     */
    'oauth' => [
        'only_wechat_browser' => false,
        'scopes'              => array_map('trim', explode(',', env('WECHAT_OAUTH_SCOPES', 'snsapi_base'))),
        'callback'            => env('WECHAT_OAUTH_CALLBACK', '/examples/oauth_callback.php'),
    ],

    //多账号
    'account' => [
        'default' => [
            'corp_id' => env('WECHAT_APPID', 'your-app-id'),         // AppID
            'secret'  => env('WECHAT_SECRET', 'your-app-secret'),     // AppSecret
            'token'   => env('WECHAT_TOKEN', 'your-token'),          // Token
            'aes_key' => env('WECHAT_AES_KEY', ''),                    // EncodingAESKey
            /*
             * OAuth 配置
             *
             * only_wechat_browser: 只在微信浏览器跳转
             * scopes：snsapi_base
             * callback：OAuth授权完成后的回调页地址(如果使用中间件，则随便填写。。。)
             */
            'oauth' => [
                'only_wechat_browser' => false,
                'scopes'              => array_map('trim', explode(',', env('WECHAT_OAUTH_SCOPES', 'snsapi_base'))),
                'callback'            => env('WECHAT_OAUTH_CALLBACK', '/examples/oauth_callback.php'),
            ],
        ],
        // ...
    ],

    /*
     * 日志配置
     *
     * level: 日志级别，可选为：
     *                 debug/info/notice/warning/error/critical/alert/emergency
     * file：日志文件位置(绝对路径!!!)，要求可写权限
     */
    'log' => [
        'level' => env('WECHAT_LOG_LEVEL', 'debug'),
        'file'  => env('WECHAT_LOG_FILE', storage_path('logs/wechat.log')),
    ],

    /*
     * 微信支付
     */
    // 'payment' => [
    //     'merchant_id'        => env('WECHAT_PAYMENT_MERCHANT_ID', 'your-mch-id'),
    //     'key'                => env('WECHAT_PAYMENT_KEY', 'key-for-signature'),
    //     'cert_path'          => env('WECHAT_PAYMENT_CERT_PATH', 'path/to/your/cert.pem'), // XXX: 绝对路径！！！！
    //     'key_path'           => env('WECHAT_PAYMENT_KEY_PATH', 'path/to/your/key'),      // XXX: 绝对路径！！！！
    //     // 'device_info'     => env('WECHAT_PAYMENT_DEVICE_INFO', ''),
    //     // 'sub_app_id'      => env('WECHAT_PAYMENT_SUB_APP_ID', ''),
    //     // 'sub_merchant_id' => env('WECHAT_PAYMENT_SUB_MERCHANT_ID', ''),
    //     // ...
    // ],
];
```

> :heart: 切换账户可以使用 `$app->account('default')`

## 日志文件

配置文件里的`/tmp/...`是绝对路径

如果在 windows 下，去把它改成`C:\foo\bar`的形式，
如果是 Linux ，你已经懂了……

如果需要按日独立存储，可以配置成`'file'  => storage_path('/tmp/entwechat/entwechat_'.date('Ymd').'.log'),`

其它同理……
