<?php

namespace EntWeChat\Agent;

use EntWeChat\Core\AbstractAPI;

/**
 * Class Agent.
 */
class Agent extends AbstractAPI
{
    const API_GET  = 'https://qyapi.weixin.qq.com/cgi-bin/agent/get';
    const API_SET  = 'https://qyapi.weixin.qq.com/cgi-bin/agent/set';
    const API_LIST = 'https://qyapi.weixin.qq.com/cgi-bin/agent/list';

    /**
     * Fetch an agent by agent id.
     *
     * @param int $agentId
     *
     * @return \EntWeChat\Support\Collection
     */
    public function get($agentId)
    {
        $params = [
            'agentid' => $agentId,
        ];

        return $this->parseJSON('get', [self::API_GET, $params]);
    }

    /**
     * Set an agent by agent id.
     *
     * @param int   $agentId
     * @param array $agentInfo
     *
     * @return \EntWeChat\Support\Collection
     */
    public function set($agentId, array $agentInfo = [])
    {
        $params = array_merge($agentInfo, [
            'agentid' => $agentId,
        ]);

        return $this->parseJSON('json', [self::API_SET, $params]);
    }

    /**
     * List agents.
     *
     * @return \EntWeChat\Support\Collection
     */
    public function lists()
    {
        return $this->parseJSON('get', [self::API_LIST]);
    }
}
