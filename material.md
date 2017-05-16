title: 素材管理
---

在微信里的图片，音乐，视频等等都需要先上传到微信服务器作为素材才可以在消息中使用。

> 请注意：

>     1. 限制：
>       - 所有文件size必须大于5个字节
>       - 图片（image）: 2M，支持 png/jpg 格式
>       - 语音（voice）：2M，播放长度不超过 60s，支持 amr 格式
>       - 视频（video）：10MB，支持MP4格式
>       - 普通文件（file）：20MB
>     2. `media_id` 是可复用的；
>     3. 素材分为 `临时素材` 与 `永久素材`， 临时素材媒体文件在后台保存时间为3天，即 3 天后 `media_id` 失效；
>     4. 永久素材的数量是有上限的，请谨慎新增。整个企业图文消息素材和图片素材数目的上限为5000，其他类型为1000；

## 获取实例

```php
<?php
use EntWeChat\Foundation\Application;

$app = new Application($options);

// 永久素材
$material = $app->material;
// 临时素材
$temporary = $app->material_temporary;
```

## 永久素材 API：

> 权限说明：所有管理组均可调用，素材归管理组所有。

### 上传图片:

> 注意：微信图片上传服务有敏感检测系统，图片内容如果含有敏感内容，如色情，商品推广，虚假信息等，上传可能失败。

```php
$result = $material->uploadImage("/path/to/your/image.jpg");  // 请使用绝对路径写法！除非你正确的理解了相对路径（好多人是没理解对的）！
var_dump($result);
// {
//    "media_id":MEDIA_ID,
//    "url":URL
// }
```

> `url` 只有上传图片素材有返回值。

### 上传声音

语音**大小不超过 2M**，**长度不超过 60 秒**，支持 `amr` 格式。

```php
$result = $material->uploadVoice("/path/to/your/voice.mp3"); // 请使用绝对路径写法！除非你正确的理解了相对路径（好多人是没理解对的）！
$mediaId = $result->media_id;
// {
//    "media_id":MEDIA_ID,
// }
```

### 上传视频

```php
$result = $material->uploadVideo("/path/to/your/video.mp4", "视频标题", "视频描述"); // 请使用绝对路径写法！除非你正确的理解了相对路径（好多人是没理解对的）！
$mediaId = $result->media_id;
// {
//    "media_id":MEDIA_ID,
// }
```

### 上传普通文件

如doc，ppt。

```php
$result = $material->uploadFile("/path/to/your/file.doc"); // 请使用绝对路径写法！除非你正确的理解了相对路径（好多人是没理解对的）！
$mediaId = $result->media_id;
// {
//    "media_id":MEDIA_ID,
// }
```

### 上传永久图文消息

图文消息没有临时一说。

```php
use EntWeChat\Message\Article;
// 上传单篇图文
$article = new Article([
    'title' => 'xxx',
    'thumb_media_id' => $mediaId,
    //...
  ]);
$material->uploadArticle($article);

// 或者多篇图文
$material->uploadArticle([$article, $article2, ...]);
```

### 修改永久图文消息

有三个参数：

- `$mediaId` 要更新的文章的 `mediaId`
- `$article` 文章内容，`Article` 实例或者 全字段数组
- `$index` 要更新的文章在图文消息中的位置（多图文消息时，此字段才有意义，单图片忽略此参数），第一篇为 0；

```php
$result = $material->updateArticle($mediaId, new Article(...));
$mediaId = $result->media_id;

// or

$result = $material->updateArticle($mediaId, [
    'title'          => 'xxx',
    'thumb_media_id' => 'xxx',
    // ...
  ]);

// 指定更新多图文中的第 2 篇
$result = $material->updateArticle($mediaId, new Article(...), 1); // 第 2 篇
```


### 上传永久文章内容图片

> 注意：微信图片上传服务有敏感检测系统，图片内容如果含有敏感内容，如色情，商品推广，虚假信息等，上传可能失败。

返回值中 url 就是上传图片的 URL，可用于后续群发中，放置到图文消息中。

```php
$result = $material->uploadArticleImage($path);
$url = $result->url;
//{
//    "url":  "http://mmbiz.qpic.cn/mmbiz/gLO17UPS6FS2xsypf378iaNhWacZ1G1UplZYWEYfwvuU6Ont96b1roYsCNFwaRrSaKTPCUdBK9DgEHicsKwWCBRQ/0"
//}
```

### 获取永久素材

```php
$resource = $material->get($mediaId);
```

如果请求的素材为图文消息，则响应如下：

```
{
   "type": "mpnews",
   "mpnews": {
       "articles": [
           {
               "thumb_media_id": "2-G6nrLmr5EC3MMb_-zK1dDdzmd0p7cNliYu9V5w7o8K0HuucGBZCzw4HmLa5C",
               "title": "Title01",
               "author": "zs",
               "digest": "airticle01",
               "content_source_url": "",
               "show_cover_pic": 0
           },
           {
               "thumb_media_id": "2-G6nrLmr5EC3MMb_-zK1dDdzmd0p7cNliYu9V5w7oovsUPf3wG4t9N3tE",
               "title": "Title02",
               "author": "Author001",
               "digest": "article02",
               "content":"Content001",
               "content_source_url": "",
               "show_cover_pic": 0
           }
       ]
   }
}
```

其他类型的素材消息，则响应的直接为素材的内容，开发者可以自行保存为文件。例如

```
$image = $material->get($mediaId);
file_put_contents('/foo/abc.jpg', $image);
```

### 获取永久素材列表

参考：[微信公众平台开发者文档：获取永久素材列表](http://qydev.weixin.qq.com/wiki/index.php?title=%E8%8E%B7%E5%8F%96%E7%B4%A0%E6%9D%90%E5%88%97%E8%A1%A8)

- `$type`   素材的类型，图片（`image`）、视频（`video`）、语音 （`voice`）、图文（`mpnews`）、文件（`file`）
- `$offset` 从全部素材的该偏移位置开始返回，可选，默认 `0`，0 表示从第一个素材 返回
- `$count`  返回素材的数量，可选，默认 `50`, 取值在 1 到 50 之间

```php
$material->lists($type, $offset, $count);
```

example:

```
$lists = $material->lists('image', 0, 10);
```

图片，文件，视频，音频类型的返回如下

```
{
   "type": TYPE,
   "itemlist": [
   {
       "media_id": MEDIA_ID,
       "filename": NAME,
       "update_time": UPDATE_TIME
   },
   //可能会有多个素材
   ]
}
```

永久图文消息素材列表的响应如下：

```
{
   "type": "mpnews",
   "itemlist": [
   {
       "media_id": MEDIA_ID,
       "content": {
           "articles": [
           {
               "title": TITLE,
               "thumb_media_id": THUMB_MEDIA_ID,
               "show_cover_pic": SHOW_COVER_PIC(0 / 1),
               "author": AUTHOR,
               "digest": DIGEST,
               "content_source_url": CONTETN_SOURCE_URL
           },
           //多图文消息会在此处有多篇文章
           ]
        },
        "update_time": UPDATE_TIME
    },
    //可能有多个图文消息item结构
  ]
}
```


### 获取素材计数

```php
$stats = $material->stats();

// {
//   "total_count":COUNT,
//   "voice_count":COUNT,
//   "video_count":COUNT,
//   "image_count":COUNT,
//   "news_count":COUNT,
//   "file_count":COUNT,
//   "mpnews_count":COUNT,
// }
```

### 删除永久素材；

```php
$material->delete($mediaId);
```


## 临时素材 API

> 权限说明：完全公开。所有管理组均可调用，素材归管理组所有。

上传的临时多媒体文件有格式和大小限制，如下：

- 图片（image）: 2MB，支持 `JPG\PNG` 格式
- 语音（voice）：2MB，播放长度不超过 `60s`，支持 `AMR` 格式
- 视频（video）：10MB，支持 `MP4` 格式
- 普通文件（file）：20MB

### 上传图片

> 注意：微信图片上传服务有敏感检测系统，图片内容如果含有敏感内容，如色情，商品推广，虚假信息等，上传可能失败。

```php
$temporary->uploadImage($path);
```

### 上传声音

```php
$temporary->uploadVoice($path);
```

### 上传视频

```php
$temporary->uploadVideo($path, $title, $description);
```

### 上传普通文件

如doc，ppt。

```php
$temporary->uploadFile($path);
```

### 获取临时素材内容

比如图片、视频、声音等二进制流内容。

```php
$content = $temporary->getStream($mediaId);
file_put_contents('/tmp/abc.jpg', $content);// 请使用绝对路径写法！除非你正确的理解了相对路径（好多人是没理解对的）！
```

### 下载临时素材到本地

其实就是上一个 API 的封装。

```php
$temporary->download($mediaId, "/tmp/", "abc.jpg");
```

参数说明：

  - `$directory` 为目标目录，
  - `$filename` 为新的文件名，可以为空，默认使用 `$mediaId` 作为文件名。


更多请参考 [微信官方文档](http://qydev.weixin.qq.com/wiki/) `管理素材文件` 章节
