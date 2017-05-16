title: 摇一摇周边
---

摇一摇周边是微信在线下的全新功能, 为线下商户提供近距离连接用户的能力, 并支持线下商户向周边用户提供个性化营销、互动及信息推荐等服务。

前期，企业号针对摇一摇周边推出的接口为：获取摇一摇设备及用户信息，后续会不断推出新的接口，敬请留意。

## 开通摇一摇周边

认证企业号可在企业号服务中心开启。

## 获取实例

```php
<?php
use EntWeChat\Foundation\Application;
// ...
$app = new Application($options);

$shakearound = $app->shakearound;

```

## API

### 获取摇一摇的设备及用户信息

获取设备信息，包括UUID、major、minor，以及距离、openID等信息。

方法

> $shakearound->getShakeInfo(string $ticket)

参数

> $ticket 摇周边业务的ticket，可在摇到的URL中得到，ticket生效时间为30分钟，每一次摇都会重新生成新的ticket

示例

```php
$result = $shakearound->getShakeInfo('6ab3d8465166598a5f4e8c1b44f44645', 1);

/* 返回结果
{
   "data": {
       "page_id ": 14211,
       "beacon_info": {
           "distance": 55.00620700469034,
           "major": 10001,
           "minor": 19007,
           "uuid": "FDA50693-A4E2-4FB1-AFCF-C6EB07647825"
       },
       "openid": "oVDmXjp7y8aG2AlBuRpMZTb1-cmA",
       "poi_id":1234
   },
   "errcode": 0,
   "errmsg": "success."
}
*/
var_dump($result->data['page_id']) // 14211
var_dump($result->data['beacon_info']['distance']) // 55.00620700469034
```

有关摇一摇周边接口信息的更多细节请参考微信官方文档相应条目： [微信官方文档](http://qydev.weixin.qq.com/wiki/)
