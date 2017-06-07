<?php

namespace EntWeChat\OA;

use EntWeChat\Core\AbstractAPI;

class API extends AbstractAPI
{
    const API_CHECKIN_DATA = 'https://qyapi.weixin.qq.com/cgi-bin/checkin/getcheckindata';
    const API_APPROVAL_DATA = 'https://qyapi.weixin.qq.com/cgi-bin/corp/getapprovaldata';

    /**
     * Get checkin data.
     *
     * @param     $startTime
     * @param     $endTime
     * @param     $userIdList
     * @param int $openCheckinDataType
     *
     * @return \EntWeChat\Support\Collection
     */
    public function getCheckinData($startTime, $endTime, $userIdList, $openCheckinDataType = 3)
    {
        $params = [
            'opencheckindatatype' => $openCheckinDataType,
            'starttime'           => $startTime,
            'endtime'             => $endTime,
            'useridlist'          => (array) $userIdList,
        ];

        return $this->parseJSON('json', [self::API_CHECKIN_DATA, $params]);
    }

    /**
     * Get approval data.
     *
     * @param      $startTime
     * @param      $endTime
     * @param null $nextSpNum
     *
     * @return \EntWeChat\Support\Collection
     */
    public function getApprovalData($startTime, $endTime, $nextSpNum = null)
    {
        $params = [
            'starttime'  => $startTime,
            'endtime'    => $endTime,
            'next_spnum' => $nextSpNum,
        ];

        return $this->parseJSON('json', [self::API_APPROVAL_DATA, $params]);
    }
}
