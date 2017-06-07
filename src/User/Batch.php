<?php

namespace EntWeChat\User;

use EntWeChat\Core\AbstractAPI;

class Batch extends AbstractAPI
{
    const API_SYNC_USER     = 'https://qyapi.weixin.qq.com/cgi-bin/batch/syncuser';
    const API_REPLACE_USER  = 'https://qyapi.weixin.qq.com/cgi-bin/batch/replaceuser';
    const API_REPLACE_PARTY = 'https://qyapi.weixin.qq.com/cgi-bin/batch/replaceparty';
    const API_GET_RESULT    = 'https://qyapi.weixin.qq.com/cgi-bin/batch/getresult';

    /**
     * Batch sync user.
     *
     * @param       $mediaId
     * @param array $callback
     *
     * @return \EntWeChat\Support\Collection
     */
    public function batchSyncUser($mediaId, $callback = [])
    {
        $params = [
            'media_id' => $mediaId,
            'callback' => $callback
        ];

        return $this->parseJSON('json', [self::API_SYNC_USER, $params]);
    }

    /**
     * Batch replace user.
     *
     * @param       $mediaId
     * @param array $callback
     *
     * @return \EntWeChat\Support\Collection
     */
    public function batchReplaceUser($mediaId, $callback = [])
    {
        $params = [
            'media_id' => $mediaId,
            'callback' => $callback
        ];

        return $this->parseJSON('json', [self::API_REPLACE_USER, $params]);
    }

    /**
     * Batch replace party.
     *
     * @param       $mediaId
     * @param array $callback
     *
     * @return \EntWeChat\Support\Collection
     */
    public function batchReplaceParty($mediaId, $callback = [])
    {
        $params = [
            'media_id' => $mediaId,
            'callback' => $callback
        ];

        return $this->parseJSON('json', [self::API_REPLACE_PARTY, $params]);
    }

    /**
     * Get result.
     *
     * @param $jobId
     *
     * @return \EntWeChat\Support\Collection
     */
    public function getResult($jobId)
    {
        $params = [
            'jobid' => $jobId,
        ];

        return $this->parseJSON('get', [self::API_GET_RESULT, $params]);
    }
}
