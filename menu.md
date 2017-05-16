title: 自定义菜单
---

企业号的每个消息型应用都可以拥有自己的菜单，企业可以调用接口来创建、删除、获取应用菜单。

注意，在操作应用的菜单时，应用必须处于回调模式；

菜单最多包括3个一级菜单，每个一级菜单最多包含5个二级菜单。

一级菜单最多4个汉字，二级菜单最多8个汉字，多出来的部分将会以“...”代替。

请注意，创建自定义菜单后，由于微信客户端缓存，需要24小时微信客户端才会展现出来。

建议测试时可以尝试取消关注企业号后再次关注，则可以看到创建后的效果。

请注意，除click和view外所有事件，仅支持微信iPhone5.4.1/Android5.4以上版本，旧版本微信成员点击后将没有回应，开发者也不能正常接收到事件推送。

## 获取菜单模块实例

```php
<?php
use EntWeChat\Foundation\Application;

// ...

$app = new Application($options);

$menu = $app->menu;
```

## API 列表

### 读取（查询）已设置菜单

```php
$menus = $menu->all($agentId);
```

### 添加菜单

#### 添加普通菜单

```php
$buttons = [
    [
        "type" => "click",
        "name" => "今日歌曲",
        "key"  => "V1001_TODAY_MUSIC"
    ],
    [
        "name"       => "菜单",
        "sub_button" => [
            [
                "type" => "view",
                "name" => "搜索",
                "url"  => "http://www.soso.com/"
            ],
            [
                "type" => "view",
                "name" => "视频",
                "url"  => "http://v.qq.com/"
            ],
            [
                "type" => "click",
                "name" => "赞一下我们",
                "key" => "V1001_GOOD"
            ],
        ],
    ],
];
$menu->add($agentId, $buttons);
```

以上将会创建一个普通菜单。

### 删除菜单

```php
$menu->destroy($agentId); // 全部
```

更多关于微信自定义菜单 API 请参考： http://qydev.weixin.qq.com/wiki `自定义菜单` 章节。
