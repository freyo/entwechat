title: 账号接入
---

如果你想使用本项目接入多个企业号或多个权限组，在本程序中，您可以为每个帐号都设置一个id，此id对应了该帐号的corpid、token等信息。
如下表

| id | corpId | secret | 其它... |
| --- | --- | --- | --- |
| 1 | `wx3cf0f39249eb0e60` | `f28f735d4f1c242f4687abb469072a29` | ... |
| 2 | `wx49eb0e63cf0f39s2` | `8f735d4687abb469f1c2422a29f4f207` | ... |
| N | `wx49eb0e63cf0f39s2` | `35f8f27d46f1c242f487a9072a29bb46` | ... |

在微信公众平台的设置中，您可以将您帐号中平台的 `url` 设置为 `您的网址/?id=xxx`，如:

```
http://www.example.com/entwechat?id=1
```

而在程序入口处，根据 `id` 查找对应帐号的 `corpid` 和 其它信息， 传入 'EntWechat\Server'，完成初始化。
