<?php

namespace EntWeChat\User;

use EntWeChat\Core\AbstractAPI;

/**
 * Class User.
 */
class User extends AbstractAPI
{
    const API_CREATE = 'https://qyapi.weixin.qq.com/cgi-bin/user/create';
    const API_UPDATE = 'https://qyapi.weixin.qq.com/cgi-bin/user/update';
    const API_DELETE = 'https://qyapi.weixin.qq.com/cgi-bin/user/delete';
    const API_BATCH_DELETE = 'https://qyapi.weixin.qq.com/cgi-bin/user/batchdelete';
    const API_GET = 'https://qyapi.weixin.qq.com/cgi-bin/user/get';
    const API_BATCH_GET = 'https://qyapi.weixin.qq.com/cgi-bin/user/simplelist';
    const API_LIST = 'https://qyapi.weixin.qq.com/cgi-bin/user/list';
    const API_TO_OPENID = 'https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_openid';
    const API_TO_USERID = 'https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_userid';

    /**
     * Fetch a user by user id.
     *
     * @param string $userId
     *
     * @return array
     */
    public function get($userId)
    {
        $params = [
            'userid' => $userId,
        ];

        return $this->parseJSON('get', [self::API_GET, $params]);
    }

    /**
     * Batch get users.
     *
     * @param int      $departmentId
     * @param int|null $fetchChild
     * @param int|null $status
     *
     * @return \EntWeChat\Support\Collection
     */
    public function batchGet($departmentId, $fetchChild = null, $status = null)
    {
        $params = [
            'department_id' => $departmentId,
            'fetch_child'   => $fetchChild,
            'status'        => $status,
        ];

        return $this->parseJSON('get', [self::API_BATCH_GET, $params]);
    }

    /**
     * List users.
     *
     * @param int      $departmentId
     * @param int|null $fetchChild
     * @param int|null $status
     *
     * @return \EntWeChat\Support\Collection
     */
    public function lists($departmentId, $fetchChild = null, $status = null)
    {
        $params = [
            'department_id' => $departmentId,
            'fetch_child'   => $fetchChild,
            'status'        => $status,
        ];

        return $this->parseJSON('get', [self::API_LIST, $params]);
    }

    /**
     * Create user.
     *
     * @param array $userInfo
     *
     * @return \EntWeChat\Support\Collection
     */
    public function create(array $userInfo = [])
    {
        $params = $userInfo;

        return $this->parseJSON('json', [self::API_CREATE, $params]);
    }

    /**
     * Create user.
     *
     * @param string $userId
     * @param array  $userInfo
     *
     * @return \EntWeChat\Support\Collection
     */
    public function update($userId, array $userInfo = [])
    {
        $params = array_merge($userInfo, ['userid' => $userId]);

        return $this->parseJSON('json', [self::API_UPDATE, $params]);
    }

    /**
     * Create user.
     *
     * @param string $userId
     *
     * @return \EntWeChat\Support\Collection
     */
    public function delete($userId)
    {
        $params = ['userid' => $userId];

        return $this->parseJSON('json', [self::API_DELETE, $params]);
    }

    /**
     * Batch delete users.
     *
     * @param array $userIds
     *
     * @return \EntWeChat\Support\Collection
     */
    public function batchDelete(array $userIds)
    {
        $params = ['useridlist' => $userIds];

        return $this->parseJSON('json', [self::API_BATCH_DELETE, $params]);
    }

    /**
     * Convert userid to openid.
     *
     * @param string $userId
     * @param int    $agentId
     *
     * @return \EntWeChat\Support\Collection
     */
    public function toOpenId($userId, $agentId)
    {
        $params = [
            'userid'  => $userId,
            'agentid' => $agentId,
        ];

        return $this->parseJSON('json', [self::API_TO_OPENID, $params]);
    }

    /**
     * Convert openid to userid.
     *
     * @param string $openId
     *
     * @return \EntWeChat\Support\Collection
     */
    public function toUserId($openId)
    {
        $params = ['openid' => $openId];

        return $this->parseJSON('json', [self::API_TO_USERID, $params]);
    }
}
