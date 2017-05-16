title: 网页授权
---

## 关于 OAuth2.0

OAuth是一个关于授权（authorization）的开放网络标准，在全世界得到广泛应用，目前的版本是2.0版。

```

     +--------+                               +---------------+
     |        |--(A)- Authorization Request ->|   Resource    |
     |        |                               |     Owner     |
     |        |<-(B)-- Authorization Grant ---|               |
     |        |                               +---------------+
     |        |
     |        |                               +---------------+
     |        |--(C)-- Authorization Grant -->| Authorization |
     | Client |                               |     Server    |
     |        |<-(D)----- Access Token -------|               |
     |        |                               +---------------+
     |        |
     |        |                               +---------------+
     |        |--(E)----- Access Token ------>|    Resource   |
     |        |                               |     Server    |
     |        |<-(F)--- Protected Resource ---|               |
     +--------+                               +---------------+
                      OAuth 授权流程
```
> 摘自：[RFC 6749](https://datatracker.ietf.org/doc/rfc6749/?include_text=1)

步骤解释：

    （A）用户打开客户端以后，客户端要求用户给予授权。
    （B）用户同意给予客户端授权。
    （C）客户端使用上一步获得的授权，向认证服务器申请令牌。
    （D）认证服务器对客户端进行认证以后，确认无误，同意发放令牌。
    （E）客户端使用令牌，向资源服务器申请获取资源。
    （F）资源服务器确认令牌无误，同意向客户端开放资源。

关于 OAuth 协议我们就简单了解到这里，如果还有不熟悉的同学，请 [Google 相关资料](https://www.google.com.hk/?gws_rd=ssl#safe=strict&q=OAuth2)

## 微信 OAuth

在微信企业号里的 OAuth 其实有两种：[网页授权](http://qydev.weixin.qq.com/wiki/index.php?title=%E8%BA%AB%E4%BB%BD%E9%AA%8C%E8%AF%81)、[网页登录](http://qydev.weixin.qq.com/wiki/index.php?title=%E6%88%90%E5%91%98%E7%99%BB%E5%BD%95%E6%8E%88%E6%9D%83)。

### 网页授权

企业应用中的URL链接（包括自定义菜单或者消息中的链接），可以通过OAuth2.0验证接口来获取成员的身份信息。

> 注意：此URL的域名，必须完全匹配企业应用设置项中的 **可信域名**（如果你的redirect_uri有端口号，那 **可信域名** 也必须加上端口号），否则跳转时会提示redirect_uri参数错误。

**授权 URL**: `https://open.weixin.qq.com/connect/oauth2/authorize`

**Scopes**:
- ***snsapi_base***：静默授权，可获取成员的基础信息
- ***snsapi_userinfo***：静默授权，可获取成员的详细信息，但不包含手机、邮箱
- ***snsapi_privateinfo***：手动授权，可获取成员的详细信息，包含手机、邮箱

> 当 `scope` 是 `snsapi_userinfo` 或 `snsapi_privateinfo` 时，参数 `agentid` 必填。

基本逻辑：

1. 用户尝试访问一个我们的业务页面，例如: `/user/profile`
2. 如果用户已经登录，则正常显示该页面
2. 系统检查当前访问的用户并未登录（从 session 或者其它方式检查），则跳转到**跳转到微信授权服务器**（上面的两种中一种**授权 URL**），并告知微信授权服务器我的**回调URL（redirect_uri=callback.php)**，此时用户看到蓝色的授权确认页面（`scope` 为 `snsapi_base` 时不显示）
4. 用户点击确定完成授权，浏览器跳转到**回调URL**: `callback.php` 并带上 `code`： `?code=CODE&state=STATE`。
5. 在 `callback.php` 中得到 `code` 后，通过 `code` 再次向微信服务器请求得到 **网页授权 access_token** 与 `openid`
6. 你可以选择拿 `openid` 去请求 API 得到用户信息（可选）
7. 将用户信息写入 SESSION。
8. 跳转到第 3 步写入的 `target_url` 页面（`/user/profile`）。

> 看懵了？没事，使用 SDK，你不用管这么多。:smile:
>
> 注意，上面的第3步：redirect_uri=callback.php实际上我们会在 `callback.php` 后面还会带上授权目标页面 `user/profile`，所以完整的 `redirect_uri` 应该是下面的这样的PHP去拼出来：`'redirect_uri='.urlencode('callback.php?target=user/profile')`
> 结果：redirect_uri=callback.php%3Ftarget%3Duser%2Fprofile

### 网页登录

使用企业号登录授权功能可方便的让用户使用企业号帐号登录第三方网站，该登录授权基于OAuth2.0协议标准构建。

如果是服务商，你需做如下准备工作：

1. 已注册成为企业号第三方服务商
2. 已创建第三方应用（参考第三方应用授权）
3. 在第三方管理端配置了微信企业号登录的参数

如果是企业内部开发者，你需做如下准备工作：

1. 在管理端 `设置` 中配置好 `登录授权域名`（注：系统管理组不可设置；普通管理组才可设置）

> 注意：跳转时必须带有 **Referer**，且 **Referer** 的域名需要与登录授权域名一致，否则会提示校验请求来源错误；授权登录之后目的跳转网址，所在域名也需与登录授权域名一致，否则跳转时会提示redirect_uri参数错误。

**授权 URL**: `https://qy.weixin.qq.com/cgi-bin/loginpage`

**Usertype**:
- ***member***：成员登录
- ***admin***：管理员登录
- ***all***：成员或管理员皆可登录

步骤说明：

1. 用户进入企业或服务商网站, 如www.ABC.com。
2. 企业或服务商网站引导用户进入登录授权页 企业或服务商可以在自己的网站首页中放置“微信企业号登录”的入口，引导用户（指企业号管理员或成员）进入登录授权页。网址为:  https://qy.weixin.qq.com/cgi-bin/loginpage?corp_id=xxxx&redirect_uri=xxxxx&state=xxxx&usertype=member 企业或服务商需要提供corp_id，跳转uri和state参数，其中uri需要经过一次urlencode作为参数，state用于企业或服务商自行校验session，防止跨域攻击。
3. 用户确认并同意授权 用户进入登录授权页后，需要确认并同意将自己的企业号和登录账号信息授权给企业或服务商，完成授权流程。
4. 授权后回调URI，得到授权码和过期时间 授权流程完成后，会进入回调URI，并在URL参数中返回授权码(redirect_url?auth_code=xxx)
5. 利用授权码调用企业号的相关API 在得到授权码后，企业或服务商可以使用授权码换取登录授权信息。

## 逻辑组成

从上面我们所描述的授权流程来看，我们至少有3个页面：

1. **业务页面**，也就是需要授权才能访问的页面。
2. **发起授权页**，此页面其实可以省略，可以做成一个中间件，全局检查未登录就发起授权。
3. **授权回调页**，接收用户授权后的状态，并获取用户信息，写入用户会话状态（SESSION）。

## 开始之前

在开始之前请一定要记住，先登录公众号后台，找到**边栏 “开发”** 模块下的 **“接口权限”**，点击 **“网页授权获取用户基本信息”** 后面的修改，添加你的网页授权域名。

> 如果你的授权地址为：`http://www.abc.com/xxxxx`，那么请填写 `www.abc.com`，也就是说请填写与网址匹配的域名，前者如果填写 `abc.com` 是通过不了的。

## SDK 中 OAuth 模块的 API

  在 SDK 中，我们使用名称为 `oauth` 和 `auth` 的模块来完成授权服务，我们主要用到以下两个 API：

### 发起授权

网页授权：

```php
$response = $app->oauth->scopes(['snsapi_userinfo'])
                          ->redirect();
```

网页登录：

```php
$response = $app->auth->with(['usertype' => 'member'])
                          ->redirect();
```

当你的应用是分布式架构且没有会话保持的情况下，你需要自行设置请求对象以实现会话共享。比如在 [Laravel](http://laravel.com) 框架中支持Session储存在Redis中，那么需要这样：

网页授权：

```php
$response = $app->oauth->scopes(['snsapi_userinfo'])
                          ->setRequest($request)
                          ->redirect();

//回调后获取user时也要设置$request对象
//$user = $app->oauth->setRequest($request)->user();
```

网页登录：

```php
$response = $app->auth->with(['usertype' => 'member'])
                          ->setRequest($request)
                          ->redirect();

//回调后获取user时也要设置$request对象
//$user = $app->auth->setRequest($request)->user();
```

它的返回值 `$response` 是一个 [Symfony\Component\HttpFoundation\RedirectResponse](http://api.symfony.com/3.0/Symfony/Component/HttpFoundation/RedirectResponse.html) 实例。

你可以选择在框架中做一些正确的响应，比如在 [Laravel](http://laravel.com) 框架中控制器方法是要求返回响应值的，那么你就直接:

```php
return $response;
```

在有的框架 (比如yii2) 中是直接 `echo` 或者 `$this->display()` 这种的时候，你就直接：

```php
$response->send(); // Laravel 里请使用：return $response;
```

### 获取已授权用户

```php
$user = $app->oauth->user();
```

## 网页授权实例

我们这里来用原生 PHP 写法举个例子，`oauth_callback` 是我们的授权回调URL (未urlencode编码的URL), `user/profile` 是我们需要授权才能访问的页面，它的 PHP 代码如下：

```php
// http://example.org/user/profile
<?php

use EntWeChat\Foundation\Application;

$config = [
  // ...
  'oauth' => [
      'scopes'   => ['snsapi_userinfo'],
      'callback' => '/oauth_callback',
  ],
  // ..
];

$app = new Application($config);
$oauth = $app->oauth;

// 未登录
if (empty($_SESSION['wechat_user'])) {

  $_SESSION['target_url'] = 'user/profile';

  return $oauth->redirect();
  // 这里不一定是return，如果你的框架action不是返回内容的话你就得使用
  // $oauth->redirect()->send();
}

// 已经登录过
$user = $_SESSION['wechat_user'];

// ...

```

授权回调页：

```php
// http://example.org/oauth_callback
<?php

use EntWeChat\Foundation\Application;

$config = [
  // ...
];

$app = new Application($config);
$oauth = $app->oauth;

// 获取 OAuth 授权结果用户信息
$user = $oauth->user();

$_SESSION['wechat_user'] = $user->toArray();

$targetUrl = empty($_SESSION['target_url']) ? '/' : $_SESSION['target_url'];

header('location:'. $targetUrl); // 跳转到 user/profile
```

上面的例子呢都是基于 `$_SESSION` 来保持会话的，在微信客户端中，你可以结合 COOKIE 来存储，但是有效期平台不一样时间也不一样，好像 Android 的失效会快一些，不过基本也够用了。


更多关于授权与登录请参考官方文档： http://qydev.weixin.qq.com/wiki
