title: 成员
---

成员信息的获取是企业微信开发中比较常用的一个功能了，以下所有成员相关操作，使用的管理组须拥有指定部门的管理/查看权限，否则无法正常使用。

## 获取实例

```php
<?php
use EntWeChat\Foundation\Application;

// ...

$app = new Application($options);

$userService = $app->user;
```

## API 列表

### 创建成员

```php
$userInfo = [
    "userid" => "zhangsan",
    "name" => "张三",
    "department" => [1, 2],
    "position" => "产品经理",
    "mobile" => "15913215421",
    "gender" => "1",
    "email" => "zhangsan@gzdev.com",
    "weixinid" => "zhangsan4dev",
    "avatar_mediaid" => "2-G6nrLmr5EC3MNb_-zL1dDdzkd0p7cNliYu9V5w7o8K0",
    "extattr" => [
        "attrs" => [
          ["name" => "爱好", "value" => "旅游"],
          ["name" => "卡号", "value" =>"1234567234"]
        ]
    ]
];

$userService->create($userInfo);
```

### 更新成员

如果非必须的字段未指定，则不更新该字段之前的设置值

```php
$userId = 'zhangsan';

$userInfo = [
    "name" => "张三",
    "department" => [1, 2],
    "position" => "产品经理",
    "mobile" => "15913215421",
    "gender" => "1",
    "email" => "zhangsan@gzdev.com",
    "weixinid" => "zhangsan4dev",
    "avatar_mediaid" => "2-G6nrLmr5EC3MNb_-zL1dDdzkd0p7cNliYu9V5w7o8K0",
    "extattr" => [
        "attrs" => [
          ["name" => "爱好", "value" => "旅游"],
          ["name" => "卡号", "value" =>"1234567234"]
        ]
    ]
];

$userService->update($userId, $userInfo);
```

### 删除成员

删除单个：

```php
$userId = 'zhangsan';
$userService->delete($userId);
```

删除多个：

```php
$userIds = [$userId1, $userId2, ...];
$userService->batchDelete($userIds);
```

### 获取成员

```php
$userService->get($userId);
```

### 获取部门成员

按部门获取成员基本信息：

```php
$userService->batchGet($departmentId, $fetchChild = null, $status = null);
```

example:

 ```php
 $users = $userService->batchGet(1);

 // result
 {
    "errcode": 0,
    "errmsg": "ok",
    "userlist": [
            {
                   "userid": "zhangsan",
                   "name": "李四",
                   "department": [1, 2]
            }
      ]
 }
 ```

 按部门获取成员详细信息：

 ```php
 $userService->lists($departmentId, $fetchChild = null, $status = null);
 ```

 ```php
 $users = $userService->lists(1);

 // result
 {
    "errcode": 0,
    "errmsg": "ok",
    "userlist": [
            {
                   "userid": "zhangsan",
                   "name": "李四",
                   "department": [1, 2],
                   "position": "后台工程师",
                   "mobile": "15913215421",
                   "gender": "1",
                   "email": "zhangsan@gzdev.com",
                   "weixinid": "lisifordev",
                   "avatar": "http://wx.qlogo.cn/mmopen/ajNVdqHZLLA3WJ6DSZUfiakYe37PKnQhBIeOQBO4czqrnZDS79FH5Wm5m4X69TBicnHFlhiafvDwklOpZeXYQQ2icg/0",
                   "status": 1,
                   "extattr": {"attrs":[{"name":"爱好","value":"旅游"},{"name":"卡号","value":"1234567234"}]}
            }
      ]
 }
 ```

关于用户管理请参考微信官方文档：http://qydev.weixin.qq.com/wiki/ `管理成员` 章节。
