<?php

namespace EntWeChat\Fundamental;

use EntWeChat\Core\AbstractAPI;

class API extends AbstractAPI
{
    const API_CALLBACK_IP = 'https://qyapi.weixin.qq.com/cgi-bin/getcallbackip';

    /**
     * Get wechat callback ip.
     *
     * @return \EntWeChat\Support\Collection
     */
    public function getCallbackIp()
    {
        return $this->parseJSON('get', [self::API_CALLBACK_IP]);
    }
}
