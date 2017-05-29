title: 部门
---

## 获取实例

```php
<?php
use EntWeChat\Foundation\Application;

// ...

$app = new Application($options);

$department = $app->user_department;
```

## API

### 获取部门列表

```php
$department->lists($partyId = null);

// example:
{
   "errcode": 0,
   "errmsg": "ok",
   "department": [
       {
           "id": 2,
           "name": "广州研发中心",
           "parentid": 1,
           "order": 10
       },
       {
           "id": 3
           "name": "邮箱产品部",
           "parentid": 2,
           "order": 40
       }
   ]
}
```

### 创建部门

```php
$department->create($name, $parentId, $order = null, $partyId = null);
```

example:

```php
$department->create("广州研发中心", 1, 1, 1);
```

### 更新部门

```php
$department->update($partyId, $partyInfo);
```

example:

```php
$partyInfo = [
   "id" => 2,
   "name" => "广州研发中心",
   "parentid" => 1,
   "order" => 1,
];
$department->update(1, $partyInfo);
```

### 删除部门

```php
$department->delete($partyId);
```

关于用户管理请参考微信官方文档：http://mp.weixin.qq.com/wiki/ `部门管理` 章节。
