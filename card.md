title: 卡券
----

企业号创建的卡券目前无需审核。企业号目前支持优惠券、团购券、代金券、折扣券、礼品券五种类型。

## 获取实例

```php
<?php
use EasyWeChat\Foundation\Application;

// ...

$app = new Application($options);

$card = $app->card;
```

## API列表

### 创建卡券

创建卡券接口是微信卡券的基础接口，用于创建一类新的卡券，获取card_id，创建成功并通过审核后，商家可以通过文档提供的其他接口将卡券下发给用户，每次成功领取，库存数量相应扣除。

```php
$card->create($cardType, $baseInfo, $especial);
```

- `cardType` string - 是要添加卡券的类型
- `baseInfo` array  - 为卡券的基本数据
- `especial` array  - 是扩展字段

example:

```php
<?php

    $cardType = 'GROUPON';

    $baseInfo = [
        'logo_url' => 'http://mmbiz.qpic.cn/mmbiz/2aJY6aCPatSeibYAyy7yct9zJXL9WsNVL4JdkTbBr184gNWS6nibcA75Hia9CqxicsqjYiaw2xuxYZiaibkmORS2oovdg/0',
        'brand_name' => '测试商户造梦空间',
        'code_type' => 'CODE_TYPE_QRCODE',
        'title' => '测试',
        'sub_title' => '测试副标题',
        'color' => 'Color010',
        'notice' => '测试使用时请出示此券',
        'service_phone' => '15311931577',
        'description' => "测试不可与其他优惠同享\n如需团购券发票，请在消费时向商户提出\n店内均可使用，仅限堂食",

        'date_info' => [
          'type' => 'DATE_TYPE_FIX_TERM',
          'fixed_term' => 90, //表示自领取后多少天内有效，不支持填写0
          'fixed_begin_term' => 0, //表示自领取后多少天开始生效，领取后当天生效填写0。
        ],

        'sku' => [
          'quantity' => '0', //自定义code时设置库存为0
        ],

        'location_id_list' => ['461907340'],  //获取门店位置poi_id，具备线下门店的商户为必填

        'get_limit' => 1,
        'use_custom_code' => true, //自定义code时必须为true
        'get_custom_code_mode' => 'GET_CUSTOM_CODE_MODE_DEPOSIT',  //自定义code时设置
        'bind_openid' => false,
        'can_share' => true,
        'can_give_friend' => false,
        'center_title' => '顶部居中按钮',
        'center_sub_title' => '按钮下方的wording',
        'center_url' => 'http://www.qq.com',
        'custom_url_name' => '立即使用',
        'custom_url' => 'http://www.qq.com',
        'custom_url_sub_title' => '6个汉字tips',
        'promotion_url_name' => '更多优惠',
        'promotion_url' => 'http://www.qq.com',
        'source' => '造梦空间',
      ];

    $especial = [
      'deal_detail' => 'deal_detail',
    ];

    $result = $card->create($cardType, $baseInfo, $especial);
```

### 创建二维码

开发者可调用该接口生成一张卡券二维码供用户扫码后添加卡券到卡包。

自定义Code码的卡券调用接口时，POST数据中需指定code，非自定义code不需指定，指定openid同理。指定后的二维码只能被用户扫描领取一次。

```php
$card->QRCode($cards);
```

- `cards` array - 卡券相关信息

example:

```php
//领取单张卡券
$cards = [
    'action_name' => 'QR_CARD',
    'expire_seconds' => 1800,
    'action_info' => [
      'card' => [
        'card_id' => 'pdkJ9uFS2WWCFfbbEfsAzrzizVyY',
        'is_unique_code' => false,
        'outer_id' => 1,
      ],
    ],
  ];

$result = $card->QRCode($cards);
```

```php
//领取多张卡券
$cards = [
    'action_name' => 'QR_MULTIPLE_CARD',
    'action_info' => [
      'multiple_card' => [
        'card_list' => [
          ['card_id' => 'pdkJ9uFS2WWCFfbbEfsAzrzizVyY'],
        ],
      ],
    ],
  ];

$result = $card->QRCode($cardList);
```

请求成功返回值示例：

```php
array(4) {
  ["ticket"]=>
  string(96) "gQHa7joAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xLzdrUFlQMHJsV3Zvanc5a2NzV1N5AAIEJUVyVwMEAKd2AA=="
  ["expire_seconds"]=>
  int(7776000)
  ["url"]=>
  string(43) "http://weixin.qq.com/q/7kPYP0rlWvojw9kcsWSy"
  ["show_qrcode_url"]=>
  string(151) "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=gQHa7joAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xLzdrUFlQMHJsV3Zvanc5a2NzV1N5AAIEJUVyVwMEAKd2AA%3D%3D"
}
```

成功返回值列表说明：

|       参数名       | 描述                                       |
| :-------------: | :--------------------------------------- |
|     ticket      | 获取的二维码ticket，凭借此ticket调用[通过ticket换取二维码接口](http://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1443433542&token=&lang=zh_CN)可以在有效时间内换取二维码。 |
| expire_seconds  | 二维码的有效时间                                 |
|       url       | 二维码图片解析后的地址，开发者可根据该地址自行生成需要的二维码图片        |
| show_qrcode_url | 二维码显示地址，点击后跳转二维码页面                       |

### ticket 换取二维码图片

获取二维码 ticket 后，开发者可用 ticket 换取二维码图片。

```php
$card->showQRCode($ticket);
```

- `ticket` string  - 获取的二维码 ticket，凭借此 ticket 可以在有效时间内换取二维码。

example:

```php
$ticket = 'gQFF8DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL01VTzN0T0hsS1BwUlBBYUszbVN5AAIEughxVwMEAKd2AA==';
$result = $card->showQRCode($ticket);
```

### ticket 换取二维码链接

```php
$card->getQRCodeUrl($ticket);  //获取的二维码ticket
```

example:

```php
$ticket = 'gQFF8DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL01VTzN0T0hsS1BwUlBBYUszbVN5AAIEughxVwMEAKd2AA==';
$card->getQRCodeUrl($ticket);
```

### JSAPI 卡券批量下发到用户

微信卡券：JSAPI 卡券

```php
$cards = [
    ['card_id' => 'pdkJ9uLRSbnB3UFEjZAgUxAJrjeY', 'outer_id' => 2],
    ['card_id' => 'pdkJ9uJ37aU-tyRj4_grs8S45k1c', 'outer_id' => 3],
];
$json = $card->jsConfigForAssign($cards); // 返回 json 格式
```

返回 json，在模板里的用法：

```html
wx.addCard({
    cardList: <?= $json ?>, // 需要打开的卡券列表
    success: function (res) {
        var cardList = res.cardList; // 添加的卡券列表信息
    }
});
```

### 核查code接口

为了避免出现导入差错，强烈建议开发者在查询完code数目的时候核查code接口校验code导入微信后台的情况。

```php
$card->checkCode($cardId, $code);
```

example:

```php
$cardId = 'pdkJ9uLCEF_HSKO7JdQOUcZ-PUzo';

$code = ['807732265476', '22222', '33333'];

$result = $card->checkCode($cardId, $code);
```

### 图文消息群发卡券

特别注意：目前该接口仅支持填入非自定义code的卡券,自定义code的卡券需先进行code导入后调用。

```php
$card->getHtml($cardId);
```

example:

```php
$cardId = 'pdkJ9uLCEF_HSKO7JdQOUcZ-PUzo';

$result = $card->getHtml($cardId);
```

### 查询Code接口

```php
$card->getCode($code, $checkConsume, $cardId);
```

- checkConsume  是否校验code核销状态，true和false

example:

```php
$code          = '736052543512';
$checkConsume = true;
$cardId       = 'pdkJ9uDgnm0pKfrTb1yV0dFMO_Gk';

$result = $card->getCode($code, $checkConsume, $cardId);
```

### 核销Code接口

```php
$card->consume($code);

// 或者指定 cardId

$card->consume($code, $cardId);
```

example:

```php
$cardId = 'pdkJ9uDmhkLj6l5bm3cq9iteQBck';
$code    = '789248558333';

$result = $card->consume($code);

//或

$result = $card->consume($code, $cardId);
```

### 查看卡券详情

开发者可以调用该接口查询某个card_id的创建信息、审核状态以及库存数量。

```php
$card->getCard($cardId);
```

example:

```php
$cardId = 'pdkJ9uLRSbnB3UFEjZAgUxAJrjeY';

$result = $card->getCard($cardId);
```

### 批量查询卡列表

```php
$card->lists($offset, $count, $statusList);
```

- `offset` int - 查询卡列表的起始偏移量，从0开始
- `count` int - 需要查询的卡片的数量
- `statusList` -  支持开发者拉出指定状态的卡券列表，详见example

example:

```php
$offset      = 0;
$count       = 10;

//CARD_STATUS_NOT_VERIFY,待审核；
//CARD_STATUS_VERIFY_FAIL,审核失败；
//CARD_STATUS_VERIFY_OK，通过审核；
//CARD_STATUS_USER_DELETE，卡券被商户删除；
//CARD_STATUS_DISPATCH，在公众平台投放过的卡券；
$statusList = 'CARD_STATUS_VERIFY_OK';

$result = $card->lists($offset, $count, $statusList);
```

### 更改卡券信息接口

支持更新所有卡券类型的部分通用字段及特殊卡券中特定字段的信息。

```php
$card->update($cardId, $type, $baseInfo);
```

- `type` string - 卡券类型

example:

```php
$cardId = 'pdkJ9uCzKWebwgNjxosee0ZuO3Os';

$type = 'groupon';

$baseInfo = [
    'logo_url' => 'http://mmbiz.qpic.cn/mmbiz/2aJY6aCPatSeibYAyy7yct9zJXL9WsNVL4JdkTbBr184gNWS6nibcA75Hia9CqxicsqjYiaw2xuxYZiaibkmORS2oovdg/0',
    'center_title' => '顶部居中按钮',
    'center_sub_title' => '按钮下方的wording',
    'center_url' => 'http://www.baidu.com',
    'custom_url_name' => '立即使用',
    'custom_url' => 'http://www.qq.com',
    'custom_url_sub_title' => '6个汉字tips',
    'promotion_url_name' => '更多优惠',
    'promotion_url' => 'http://www.qq.com',
];

$result = $card->update($cardId, $type, $baseInfo);
```

### 修改库存接口

```php
$card->increaseStock($cardId, $amount); // 增加库存
$card->reduceStock($cardId, $amount); // 减少库存
```

- `cardId` string - 卡券 ID
- `amount` int - 修改多少库存

example:

```php
$cardId = 'pdkJ9uLRSbnB3UFEjZAgUxAJrjeY';

$result = $card->increaseStock($cardId, 100);
```

### 删除卡券接口

```php
$card->delete($cardId);
```

example:

```php
$cardId = 'pdkJ9uItT7iUpBp4GjZp8Cae0Vig';

$result = $card->delete($cardId);
```

关于卡券接口的使用请参阅官方文档：http://qydev.weixin.qq.com/wiki/
