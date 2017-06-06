<?php

namespace EntWeChat\Broadcast;

use EntWeChat\Core\AbstractAPI;

/**
 * Class Broadcast.
 */
class Broadcast extends AbstractAPI
{
    const API_MESSAGE_SEND = 'https://qyapi.weixin.qq.com/cgi-bin/message/send';

    const MSG_TYPE_TEXT = 'text';   // 文本
    const MSG_TYPE_NEWS = 'news';   // 图文
    const MSG_TYPE_VOICE = 'voice';  // 语音
    const MSG_TYPE_IMAGE = 'image';  // 图片
    const MSG_TYPE_VIDEO = 'video';  // 视频
    const MSG_TYPE_CARD = 'card';   // 卡券
    const MSG_TYPE_FILE = 'file';   // 文件
    const MSG_TYPE_MPNEWS = 'mpnews'; // 图文
	const MSG_TYPE_TEXTCARD = 'textcard'; // 文本卡片

    /**
     * Get message builder.
     *
     * @param \EntWeChat\Message\AbstractMessage|string $message
     *
     * @throws \EntWeChat\Core\Exceptions\InvalidArgumentException
     *
     * @return MessageBuilder
     */
    public function message($message)
    {
        $messageBuilder = new MessageBuilder($this);

        return $messageBuilder->message($message);
    }

    /**
     * Send a message.
     *
     * @param string|array $message
     *
     * @return \EntWeChat\Support\Collection
     */
    public function send($message)
    {
        return $this->parseJSON('json', [self::API_MESSAGE_SEND, $message]);
    }
}
