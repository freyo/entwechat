<?php

namespace EntWeChat\Menu;

use EntWeChat\Core\AbstractAPI;

/**
 * Class Menu.
 */
class Menu extends AbstractAPI
{
    const API_CREATE = 'https://qyapi.weixin.qq.com/cgi-bin/menu/create';
    const API_GET = 'https://qyapi.weixin.qq.com/cgi-bin/menu/get';
    const API_DELETE = 'https://qyapi.weixin.qq.com/cgi-bin/menu/delete';

    /**
     * Get all menus.
     *
     * @return \EntWeChat\Support\Collection
     */
    public function all($agentId)
    {
        return $this->parseJSON('get', [self::API_GET, ['agentid' => $agentId]]);
    }

    /**
     * Add menu.
     *
     * @param int   $agentId
     * @param array $buttons
     *
     * @return bool
     */
    public function add($agentId, array $buttons)
    {
        return $this->parseJSON('json', [self::API_CREATE, ['button' => $buttons], JSON_UNESCAPED_UNICODE, ['agentid' => $agentId]]);
    }

    /**
     * Destroy menu.
     *
     * @param int $agentId
     *
     * @return bool
     */
    public function destroy($agentId)
    {
        return $this->parseJSON('get', [self::API_DELETE, ['agentid' => $agentId]]);
    }
}
