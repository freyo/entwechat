<?php

namespace EntWeChat\Suite\Api;

class BaseApi extends AbstractSuite
{
    /**
     * Get auth info api.
     */
    const GET_AUTH_INFO = 'https://qyapi.weixin.qq.com/cgi-bin/service/get_auth_info';

    /**
     * Get permanent token api.
     */
    const GET_PERMANENT_TOKEN = 'https://qyapi.weixin.qq.com/cgi-bin/service/get_permanent_code';

    /**
     * Set session info api.
     */
    const SET_SESSION_INFO = 'https://qyapi.weixin.qq.com/cgi-bin/service/set_session_info';

    /**
     * Get corp token api.
     */
    const GET_CORP_TOKEN = 'https://qyapi.weixin.qq.com/cgi-bin/service/get_corp_token';

    /**
     * Get provider token api.
     */
    const GET_PROVIDER_TOKEN = 'https://qyapi.weixin.qq.com/cgi-bin/service/get_provider_token';

    /**
     * @param $authCorpId
     * @param $permanentCode
     *
     * @return \EntWeChat\Support\Collection
     */
    public function getAuthorizerInfo($authCorpId, $permanentCode)
    {
        $params = [
            'suite_id'       => $this->getSuiteId(),
            'auth_corpid'    => $authCorpId,
            'permanent_code' => $permanentCode,
        ];

        return $this->parseJSON('json', [self::GET_AUTH_INFO, $params]);
    }

    /**
     * @param null $authCode
     *
     * @return \EntWeChat\Support\Collection
     */
    public function getAuthorizationInfo($authCode = null)
    {
        $params = [
            'suite_id'  => $this->getSuiteId(),
            'auth_code' => $authCode ?: $this->request->get('auth_code'),
        ];

        return $this->parseJSON('json', [self::GET_PERMANENT_TOKEN, $params]);
    }

    /**
     * @param $preAuthCode
     * @param $sessionInfo
     *
     * @return \EntWeChat\Support\Collection
     */
    public function setAuthorizerOption($preAuthCode, $sessionInfo)
    {
        $params = [
            'pre_auth_code' => $preAuthCode,
            'session_info'  => $sessionInfo,
        ];

        return $this->parseJSON('json', [self::SET_SESSION_INFO, $params]);
    }

    /**
     * @param $authCorpId
     * @param $permanentCode
     *
     * @return \EntWeChat\Support\Collection
     */
    public function getAuthorizerToken($authCorpId, $permanentCode)
    {
        $params = [
            'suite_id'       => $this->getSuiteId(),
            'auth_corpid'    => $authCorpId,
            'permanent_code' => $permanentCode,
        ];

        return $this->parseJSON('json', [self::GET_CORP_TOKEN, $params]);
    }

    /**
     * @param $corpId
     * @param $providerSecret
     *
     * @return \EntWeChat\Support\Collection
     */
    public function getProviderToken($corpId, $providerSecret)
    {
        $params = [
            'corpid'          => $corpId,
            'provider_secret' => $providerSecret,
        ];

        return $this->parseJSON('json', [self::GET_PROVIDER_TOKEN, $params]);
    }
}
