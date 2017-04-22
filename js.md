title: JSSDK
---

## 获取实例

```php
<?php
use EntWeChat\Foundation\Application;
//...
$app = new Application($options);

$js = $app->js;
$js_contact = $app->js_contact;
```

## API

- `$js->config(array $APIs, $debug = false, $beta = false, $json = true);` 获取JSSDK的配置数组，默认返回 JSON 字符串，当 `$json` 为 `false` 时返回数组，你可以直接使用到网页中。
- `$js->setUrl($url)` 设置当前URL，如果不想用默认读取的URL，可以使用此方法手动设置，通常不需要。
- `$js_contact->config(array $params, $json = true);` 验证企业号管理组权限，用于打开企业通讯录选人。

example:

我们可以生成js配置文件：

```js
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
    wx.config(<?php echo $js->config(array('onMenuShareQQ', 'onMenuShareWeibo'), true) ?>);
</script>
```
结果如下：

```js
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
wx.config({
    debug: true,
    appId: 'wx3cf0f39249eb0e60',
    timestamp: 1430009304,
    nonceStr: 'qey94m021ik',
    signature: '4F76593A4245644FAE4E1BC940F6422A0C3EC03E',
    jsApiList: ['onMenuShareQQ', 'onMenuShareWeibo']
});
</script>
```

## 企业号专有接口

### 创建企业会话

```js
wx.openEnterpriseChat({
    userIds: 'zhangshan;lisi;wangwu',    // 必填，参与会话的成员列表。格式为userid1;userid2;...，用分号隔开，最大限制为2000个。userid单个时为单聊，多个时为群聊。
    groupName: 'openEnterpriseChat讨论组',  // 必填，会话名称。单聊时该参数传入空字符串""即可。
    success: function(res) {
        // 回调
    },
    fail: function(res) {
        if(res.errMsg.indexOf('function not exist') > -1){
            alert('版本过低请升级')
        }
    }
});
```

### 打开企业通讯录选人

> 注意：该JS-SDK需要验证企业号管理组权限

```js
var evalWXjsApi = function(jsApiFun) {
    if (typeof WeixinJSBridge == "object" && typeof WeixinJSBridge.invoke == "function") {
        jsApiFun();
    } else {
        document.attachEvent && document.attachEvent("WeixinJSBridgeReady", jsApiFun);
        document.addEventListener && document.addEventListener("WeixinJSBridgeReady", jsApiFun);
    }
}

document.querySelector('#openEnterpriseContact_invoke').onclick = function() {
    evalWXjsApi(function() {
        WeixinJSBridge.invoke("openEnterpriseContact", <?php echo $js_contact->config([
          'departmentIds' => [1],    // 非必填，可选部门ID列表（如果ID为0，表示可选管理组权限下所有部门）
          'tagIds' => [1],    // 非必填，可选标签ID列表（如果ID为0，表示可选所有标签）
          'userIds' => ['zhangsan','lisi'],    // 非必填，可选用户ID列表
          'mode' => 'single',    // 必填，选择模式，single表示单选，multi表示多选
          'type' => ['department','tag','user'],    // 必填，选择限制类型，指定department、tag、user中的一个或者多个
          'selectedDepartmentIds' => [],    // 非必填，已选部门ID列表
          'selectedTagIds' => [],    // 非必填，已选标签ID列表
          'selectedUserIds' => [],    // 非必填，已选用户ID列表
        ]); ?>, function(res) {
            if (res.err_msg.indexOf('function_not_exist') > -1) {
                alert('版本过低请升级');
            } else if (res.err_msg.indexOf('openEnterpriseContact:fail') > -1) {
                return;
            }
            var result = JSON.parse(res.result);    // 返回字符串，开发者需自行调用JSON.parse解析
            var selectAll = result.selectAll;     // 是否全选（如果是，其余结果不再填充）
            if (!selectAll)
            {
                var selectedDepartmentList = result.departmentList;    // 已选的部门列表
                for (var i = 0; i < selectedDepartmentList.length; i++) {
                    var department = selectedDepartmentList[i];
                    var departmentId = department.id;    // 已选的单个部门ID
                    var departemntName = department.name;    // 已选的单个部门名称
                }
                var selectedTagList = result.tagList;    // 已选的标签列表
                for (var i = 0; i < selectedTagList.length; i++) {
                    var tag = selectedTagList[i];
                    var tagId = tag.id;    // 已选的单个标签ID
                    var tagName = tag.name;    // 已选的单个标签名称
                }
                var selectedUserList = result.userList;    // 已选的成员列表
                for (var i = 0; i < selectedUserList.length; i++) {
                    var user = selectedUserList[i];
                    var userId = user.id;    // 已选的单个成员ID
                    var userName = user.name;    // 已选的单个成员名称
                }
            }
        })
    });
}
```

### 向当前企业会话发送消息

> 注意：该JS-SDK仅适用于从应用的“关联到会话”入口进入的网页

```js
var evalWXjsApi = function(jsApiFun) {
    if (typeof WeixinJSBridge == "object" && typeof WeixinJSBridge.invoke == "function") {
        jsApiFun();
    } else {
        document.attachEvent && document.attachEvent("WeixinJSBridgeReady", jsApiFun);
        document.addEventListener && document.addEventListener("WeixinJSBridgeReady", jsApiFun);
    }
}

// 发送文本消息
document.querySelector('#sendEnterpriseChat_text_invoke').onclick = function() {
    evalWXjsApi(function() {
        WeixinJSBridge.invoke("sendEnterpriseChat", {
            "type": "text",    // 必填，text表示发送的内容是文字
            "data": {
            	"content": "text sent to enterprise chat",    // 必填，表示发送的文字内容
            }
        }, function(res) {
            alert(JSON.stringify(res));
        })
    });
}

// 发送链接消息
document.querySelector('#sendEnterpriseChat_link_invoke').onclick = function() {
    evalWXjsApi(function() {
        WeixinJSBridge.invoke("sendEnterpriseChat", {
            "type": "link",    // 必填，link表示发送的内容是链接
            "data": {
             	"imgUrl": "http://shp.qpic.cn/bizmp/PiajxSqBRaEJ60dDSjCqczZFTTHsyX5MvxibUq1L3jwokHORAYOV0OdQ/",    // 必填，表示消息的图标
             	"title": "test title",    // 非必填，但与desc不能同时为空
             	"desc": "test desc",    // 非必填，但与title不能同时为空
             	"link": "http://qy.weixin.qq.com",    // 必填，表示消息的链接
            }
        }, function(res) {
            alert(JSON.stringify(res));
        })
    });
}
```

更多 JSSDK 的使用请参考 [微信官方文档](http://qydev.weixin.qq.com/wiki/) 中 **JSSDK章节**
