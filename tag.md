title: 标签
---

## 获取实例

```php
<?php
use EntWeChat\Foundation\Application;

// ...

$app = new Application($options);

$tag = $app->user_tag; // $user['user_tag']
```

## API

### 获取所有标签

```php
$tag->lists();
```

example:

```php
$tags = $tag->lists();
```

result:
```json
{
   "errcode": 0,
   "errmsg": "ok",
   "taglist":[
      {"tagid":1,"tagname":"a"},
      {"tagid":2,"tagname":"b"}
   ]
}
```

### 创建标签

```php
$tag->create($name, $tagId);
```

example:

```php
$tag->create('测试标签');
```

### 修改标签信息

```php
$tag->update($tagId, $name);
```

example:

```php
$tag->update(12, "新的名称");
```

### 删除标签

```php
$tag->delete($tagId);
```

example:

```php
$tag->delete($tagId);
```

### 获取标签成员

```php
$tag->usersOfTag($userId);
```

result:
```json
{
   "errcode": 0,
   "errmsg": "ok",
   "userlist": [
         {
             "userid": "zhangsan",
             "name": "李四"
         }
     ],
   "partylist": [2]
}
```

### 增加标签成员

```php
$userIds = [$userId1, $userId2, ...];
$partyIds = [$partyId1, $partyId2, ...];
$tag->batchTagUsers($tagId, $userIds, $partyIds);
```

### 删除标签成员

```php
$userIds = [$userId1, $userId2, ...];
$partyIds = [$partyId1, $partyId2, ...];
$tag->batchUntagUsers($tagId, $userIds, $partyIds);
```

关于用户管理请参考微信官方文档：http://qydev.weixin.qq.com/wiki/ `管理标签` 章节。
