<?php

namespace EntWeChat\Soter;

use EntWeChat\Core\AbstractAPI;

/**
 * Class Soter.
 */
class Soter extends AbstractAPI
{
    const API_VERIFY_SIGNATURE = 'https://qyapi.weixin.qq.com/cgi-bin/soter/verify_signature';

    /**
     * Verify signature.
     *
     * @param string $openid
     * @param string $jsonString
     * @param string $jsonSignature
     *
     * @return \EntWeChat\Support\Collection
     */
    public function verify($openid, $jsonString, $jsonSignature)
    {
        $params = [
            'openid' => $openid,
            'json_string' => $jsonString,
            'json_signature' => $jsonSignature,
        ];

        return $this->parseJSON('json', [self::API_VERIFY_SIGNATURE, $params]);
    }
}
