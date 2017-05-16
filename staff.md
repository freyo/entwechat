title: 客服
---

## 获取实例

```php
<?php
use EntWeChat\Foundation\Application;
// ...
$app = new Application($options);

$staff = $app->staff;
```

## API

### 获取客服列表

客服类型($type)：
     `internal` 只获取内部客服列表；
     `external` 只获取外部客服列表；
     不填时，同时返回内部、外部客服列表。

```php
$staff->lists($type);
```

### 发送和同步消息

用户类型($type)：
    `kf` 客服；
    `userid` 客户，企业员工userid；
    `openid` 客户，公众号openid。

> 注意： `$staff->by($type, $id)` 和 `$staff->to($type, $id)` 中 `$type` 有且只有一个类型为 `kf` 。
>
> 当 `$staff->by($type, $id)` 中 `$type` 为 `kf` 时，表示向客服发送用户咨询的问题消息。
>
> 当 `$staff->to($type, $id)` 中 `$type` 为 `kf` 时，表示客服从其它IM工具回复客户，并同步消息到客服的微信上。

```php
$staff->message($message)->by($type, $id)->to($type, $id)->send();
```

> `$message` 为消息对象，请参考：[消息](messages.html)

关于更多客服接口信息请参考微信官方文档：http://qydev.weixin.qq.com/wiki/
