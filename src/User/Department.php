<?php

namespace EntWeChat\User;

use EntWeChat\Core\AbstractAPI;

/**
 * Class Department.
 */
class Department extends AbstractAPI
{
    const API_GET    = 'https://qyapi.weixin.qq.com/cgi-bin/department/list';
    const API_CREATE = 'https://qyapi.weixin.qq.com/cgi-bin/department/create';
    const API_UPDATE = 'https://qyapi.weixin.qq.com/cgi-bin/department/update';
    const API_DELETE = 'https://qyapi.weixin.qq.com/cgi-bin/department/delete';

    /**
     * Create Department.
     *
     * @param string   $name
     * @param int      $parentId
     * @param int|null $order
     * @param int|null $partyId
     *
     * @return int
     */
    public function create($name, $parentId, $order = null, $partyId = null)
    {
        $params = [
            'name' => $name,
            'parentid' => $parentId,
            'order' => $order,
            'id' => $partyId,
        ];

        return $this->parseJSON('json', [self::API_CREATE, $params]);
    }

    /**
     * List all Departments.
     *
     * @return array
     */
    public function lists()
    {
        return $this->parseJSON('get', [self::API_GET]);
    }

    /**
     * Update a Department.
     *
     * @param int   $partyId
     * @param array $name
     *
     * @return bool
     */
    public function update($partyId, array $partyInfo = [])
    {
        $params = array_merge($partyInfo, ['id' => $partyId]);

        return $this->parseJSON('json', [self::API_UPDATE, $params]);
    }

    /**
     * Delete Department.
     *
     * @param int $groupId
     *
     * @return bool
     */
    public function delete($partyId)
    {
        $params = [
            'id' => $partyId,
        ];

        return $this->parseJSON('json', [self::API_DELETE, $params]);
    }
}
