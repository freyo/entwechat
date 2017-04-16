<?php

namespace EntWeChat\ShakeAround;

use EntWeChat\Core\AbstractAPI;

/**
 * Class ShakeAround.
 */
class ShakeAround extends AbstractAPI
{
    const API_GET_SHAKE_INFO = 'https://qyapi.weixin.qq.com/cgi-bin/shakearound/getshakeinfo';

    /**
     * Get shake info.
     *
     * @param string $ticket
     *
     * @return \EntWeChat\Support\Collection
     */
    public function getShakeInfo($ticket)
    {
        $params = [
            'ticket' => $ticket,
        ];

        return $this->parseJSON('json', [self::API_GET_SHAKE_INFO, $params]);
    }
}
