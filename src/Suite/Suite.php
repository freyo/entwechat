<?php

namespace EntWeChat\Suite;

use EntWeChat\Support\Traits\PrefixedContainer;

/**
 * Class Suite.
 *
 * @property \EntWeChat\Suite\Api\BaseApi          $api
 * @property \EntWeChat\Suite\Api\PreAuthorization $pre_auth
 * @property \EntWeChat\Suite\Guard                $server
 * @property \EntWeChat\Suite\AccessToken          $access_token
 *
 * @method \EntWeChat\Support\Collection getAuthorizationInfo($authCode = null)
 * @method \EntWeChat\Support\Collection getAuthorizerInfo($authorizerAppId)
 * @method \EntWeChat\Support\Collection getAuthorizerOption($authorizerAppId, $optionName)
 * @method \EntWeChat\Support\Collection setAuthorizerOption($authorizerAppId, $optionName, $optionValue)
 */
class Suite
{
    use PrefixedContainer;

    /**
     * Create an instance of the EntWeChat for the given authorizer.
     *
     * @param string $appId        Authorizer AppId
     * @param string $refreshToken Authorizer refresh-token
     *
     * @return \EntWeChat\Foundation\Application
     */
    public function createAuthorizerApplication($appId, $refreshToken)
    {
        $this->fetch('authorization')
             ->setAuthorizerAppId($appId)
             ->setAuthorizerRefreshToken($refreshToken);

        $application                 = $this->fetch('app');
        $application['access_token'] = $this->fetch('authorizer_access_token');
        $application['oauth']        = $this->fetch('oauth');

        return $application;
    }

    /**
     * Quick access to the base-api.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this->api, $method], $args);
    }
}
