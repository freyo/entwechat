<?php

namespace EntWeChat\User;

use EntWeChat\Core\AbstractAPI;

/**
 * Class Tag.
 */
class Tag extends AbstractAPI
{
    const API_GET = 'https://qyapi.weixin.qq.com/cgi-bin/tag/list';
    const API_CREATE = 'https://qyapi.weixin.qq.com/cgi-bin/tag/create';
    const API_UPDATE = 'https://qyapi.weixin.qq.com/cgi-bin/tag/update';
    const API_DELETE = 'https://qyapi.weixin.qq.com/cgi-bin/tag/delete';
    const API_MEMBER_BATCH_TAG = 'https://qyapi.weixin.qq.com/cgi-bin/tag/addtagusers';
    const API_MEMBER_BATCH_UNTAG = 'https://qyapi.weixin.qq.com/cgi-bin/tag/deltagusers';
    const API_USERS_OF_TAG = 'https://qyapi.weixin.qq.com/cgi-bin/tag/get';

    /**
     * Create tag.
     *
     * @param string $name
     * @param null   $tagI
     *
     * @return int
     */
    public function create($name, $tagId = null)
    {
        $params = [
            'tagname' => $name,
            'tagid'   => $tagId,
        ];

        return $this->parseJSON('json', [self::API_CREATE, $params]);
    }

    /**
     * List all tags.
     *
     * @return array
     */
    public function lists()
    {
        return $this->parseJSON('get', [self::API_GET]);
    }

    /**
     * Update a tag name.
     *
     * @param int    $tagId
     * @param string $name
     *
     * @return bool
     */
    public function update($tagId, $name)
    {
        $params = [
            'tagid'   => $tagId,
            'tagname' => $name,
        ];

        return $this->parseJSON('json', [self::API_UPDATE, $params]);
    }

    /**
     * Delete tag.
     *
     * @param int $tagId
     *
     * @return bool
     */
    public function delete($tagId)
    {
        $params = [
            'tagid' => $tagId,
        ];

        return $this->parseJSON('json', [self::API_DELETE, $params]);
    }

    /**
     * Get users from a tag.
     *
     * @param string $tagId
     *
     * @return int
     */
    public function usersOfTag($tagId)
    {
        $params = ['tagid' => $tagId];

        return $this->parseJSON('json', [self::API_USERS_OF_TAG, $params]);
    }

    /**
     * Batch tag users.
     *
     * @param       $tagId
     * @param array $userIds
     * @param array $partyIds
     *
     * @return bool
     */
    public function batchTagUsers($tagId, array $userIds = [], array $partyIds = [])
    {
        $params = [
            'tagid'     => $tagId,
            'userlist'  => $userIds,
            'partylist' => $partyIds,
        ];

        return $this->parseJSON('json', [self::API_MEMBER_BATCH_TAG, $params]);
    }

    /**
     * Untag users from a tag.
     *
     * @param       $tagId
     * @param array $userIds
     * @param array $partyIds
     *
     * @return bool
     */
    public function batchUntagUsers($tagId, array $userIds = [], array $partyIds = [])
    {
        $params = [
            'tagid'     => $tagId,
            'userlist'  => $userIds,
            'partylist' => $partyIds,
        ];

        return $this->parseJSON('json', [self::API_MEMBER_BATCH_UNTAG, $params]);
    }
}
