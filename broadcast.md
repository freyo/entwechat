title: 群发
---

企业可以主动发消息给成员，每天可发的数量为：帐号上限数*30人次/天。

消息型应用支持文本、图片、语音、视频、文件、图文等消息类型。

除了news类型，其它类型的消息可在发送时加上保密选项，保密消息会被打上水印，并且只有接收者才能阅读。

主页型应用只支持文本消息类型，且文本长度不超过20个字。

收件人必须处于应用的可见范围内，并且管理组对应用有使用权限、对收件人有查看权限，否则本次调用失败。

如果无权限或收件人不存在，则本次发送失败，返回无效的userid列表（注：由于userid不区分大小写，返回的列表都统一转为小写）；如果未关注，发送仍然执行。

## 获取实例

```php
<?php
use EntWeChat\Foundation\Application;
// ...
$app = new Application($options);

$broadcast = $app->broadcast;

```

## API

### 群发消息给所有成员

```php
$broadcast->by($agentId)->message($message)->toAll()->send();
```

### 群发消息给指定成员

当群发对象为全员时忽略本参数；非`string`或`numeric`类型的用户ID将被忽略

```php
$broadcast->by($agentId)->message($message)->toUser($userId1, $userId2)->send();

//或传入数组
$broadcast->by($agentId)->message($message)->toUser([$userId1, $userId2])->send();
```

### 群发消息给指定部门下成员

当群发对象为全员时忽略本参数；非`numeric`类型的部门ID将被忽略

```php
$broadcast->by($agentId)->message($message)->toParty($partyId1, $partyId2)->send();

//或传入数组
$broadcast->by($agentId)->message($message)->toParty([$partyId1, $partyId2])->send();
```

### 群发消息给指定标签下成员

当群发对象为全员时忽略本参数；非`numeric`类型的标签ID将被忽略

```php
$broadcast->by($agentId)->message($message)->toTag($tagId1, $tagId2)->send();

//或传入数组
$broadcast->by($agentId)->message($message)->toTag([$tagId1, $tagId2])->send();
```

### 多条件群发

可以同时群发给指定成员、指定标签下成员、指定部门下成员，非交集

```php
$broadcast->by($agentId)->message($message)
                        ->toUser($userId1, $userId2)
                        ->toParty($userId1, $userId2)
                        ->toTag($tagId1, $tagId2)
                        ->send();
```

### 群发保密消息

```php
$broadcast->by($agentId)->message($message)
                        ->safe()
                        ->toUser($userId1, $userId2)
                        ->send();
```

### 注意事项

> 重复调用方法后者会覆盖前者

如下例，最终只会发消息给`UserId`为`$userId2`的成员

```php
$broadcast->by($agentId)->message($message)
                        ->toUser($userId1)
                        ->toUser($userId2)
                        ->send();
```

有关群发信息的更多细节请参考微信官方文档：http://qydev.weixin.qq.com/wiki/
