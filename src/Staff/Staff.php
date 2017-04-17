<?php

namespace EntWeChat\Staff;

use EntWeChat\Core\AbstractAPI;

/**
 * Class Staff.
 */
class Staff extends AbstractAPI
{
    const API_LISTS = 'https://qyapi.weixin.qq.com/cgi-bin/kf/list';
    const API_MESSAGE_SEND = 'https://qyapi.weixin.qq.com/cgi-bin/kf/send';

    //用户类型
    const USER_TYPE_STAFF = 'kf';      // 客服
    const USER_TYPE_USERID = 'userid';  // 客户UserId
    const USER_TYPE_OPENID = 'openid';  // 客户OpenId

    //客服类型
    const STAFF_TYPE_INTERNAL = 'internal';  // 内部
    const STAFF_TYPE_EXTERNAL = 'external';  // 外部

    /**
     * List all staffs.
     *
     * @param string|null $type
     *
     * @return \EntWeChat\Support\Collection
     */
    public function lists($type = null)
    {
        $params = [
            'type' => $type,
        ];

        return $this->parseJSON('get', [self::API_LISTS, $params]);
    }

    /**
     * Get message builder.
     *
     * @param \EntWeChat\Message\AbstractMessage|string $message
     *
     * @throws \EntWeChat\Core\Exceptions\InvalidArgumentException
     *
     * @return \EntWeChat\Staff\MessageBuilder
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
