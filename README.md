# EntWeChat

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Quality Score](https://img.shields.io/scrutinizer/g/freyo/entwechat.svg?style=flat-square)](https://scrutinizer-ci.com/g/freyo/entwechat)
[![Packagist Version](https://img.shields.io/packagist/v/freyo/entwechat.svg?style=flat-square)](https://packagist.org/packages/freyo/entwechat)
[![Total Downloads](https://img.shields.io/packagist/dt/freyo/entwechat.svg?style=flat-square)](https://packagist.org/packages/freyo/entwechat)

WeChat Enterprise SDK based on **[EasyWeChat 3.X](https://github.com/overtrue/wechat)**

## Requirement

1. PHP >= 5.5.9
2. **[composer](https://getcomposer.org/)**
3. openssl 拓展
4. fileinfo 拓展（素材管理模块需要用到）

> SDK 对所使用的框架并无特别要求

## Installation

```shell
composer require "freyo/entwechat" -vvv
```

## Usage

使用示例:

```php
<?php

use EntWeChat\Foundation\Application;

$options = [
    'debug'     => true,
    'corp_id'   => 'wx3cf0f39249eb0e60',
    'secret'    => 'Nyn7Yuw-YbqDZeWiWZM6HqghGkXTFdZbaXpnk6w4G1IQwgtTuOl_TN09ciwpQ-5X',
    // ...
];

$app = new Application($options);

//微信端网页授权
$app->oauth->setRedirectUrl('http://example.org')
           ->scopes(['snsapi_base'])
           ->redirect()
           ->send();
//获取授权用户信息
$user = $app->oauth->user();
//$user->UserId //企业成员授权时
//$user->DeviceId
//$user->OpenId //非企业成员授权时

//PC端扫码登录
$app->auth->with(['usertype' => 'member'])
          ->setRedirectUrl('http://example.org')
          ->redirect()
          ->send();
//获取登录用户信息
$user = $app->auth->user();
//登录用户为企业号成员时
//$user->usertype
//$user->user_info['userid'] //name,email,avatar
//$user->redirect_login_info['login_ticket']
//$user->corp_info['corpid']

//通过userid获取用户信息
$app->user->get('userid');

//获取指定部门id下成员
$deptId = 0;
$app->user->lists($deptId);

//获取部门列表
$app->user_department->lists();

//获取标签列表
$app->user_tag->lists();

//获取指定标签id下成员
$tagId = 0;
$app->user_tag->usersOfTag($tagId);

//发送消息给指定用户通过指定应用id
$news = new \EntWeChat\Message\News([
    'title'       => '图文标题',
    'description' => '图文描述',
    'url'         => 'http://example.org',
    'image'       => 'http://mat1.gtimg.com/cq/js/news/tina/wenhua2.jpg',
]);

$agentId = 0;
$app->broadcast->message($news)->by($agentId)->toUser('userid')->send(); //单图文
$app->broadcast->message([$news, $news])->by($agentId)->toUser('userid')->send(); //多图文
$app->broadcast->message($news)->by($agentId)->toAll()->send(); //发送给所有人
$app->broadcast->message($news) //发送给指定用户、部门、标签
               ->by($agentId)
               ->toUser('userid1', 'userid2')
               ->toParty($deptId)
               ->toTag($tagId)
               ->send();

//服务端回调
$server = $app->server;
$user = $app->user;

$server->setMessageHandler(function($message) use ($user) {
    // $message->FromUserName // 用户的 openid
    // $message->MsgType // 消息类型：event, text....
    $fromUser = $user->get($message->FromUserName);
    return "{$fromUser->nickname} 您好！欢迎关注！";
});

$server->serve()->send();
```

文档完善中。
