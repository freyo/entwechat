<?php

namespace EntWeChat\Service;

use EntWeChat\Core\Exceptions\InvalidStateException;

/**
 * Class Authentication.
 */
class Authentication extends AbstractService
{
    const LOGIN_URL = 'https://qy.weixin.qq.com/cgi-bin/loginpage';

    const API_GET_LOGIN_INFO = 'https://qyapi.weixin.qq.com/cgi-bin/service/get_login_info';
    const API_GET_SSO_URL = 'https://qyapi.weixin.qq.com/cgi-bin/service/get_login_url';

    /**
     * Indicates if the session state should be utilized.
     *
     * @var bool
     */
    protected $stateless = true;

    /**
     * Get the SSO URL.
     *
     * @param string   $loginTicket
     * @param string   $target
     * @param int|null $agentId
     *
     * @return \EntWeChat\Support\Collection
     */
    public function getSSOUrl($loginTicket, $target, $agentId = null)
    {
        $params = [
            'login_ticket' => $loginTicket,
            'target'       => $target,
            'agentid'      => $agentId,
        ];

        return $this->parseJSON('json', [self::API_GET_SSO_URL, $params]);
    }

    /**
     * @param string|null $authCode
     *
     * @throws InvalidStateException
     *
     * @return \EntWeChat\Support\Collection
     */
    public function user($authCode = null)
    {
        if ($this->hasInvalidState()) {
            throw new InvalidStateException();
        }

        $params = [
            'auth_code' => $authCode ?: $this->request->get('auth_code'),
        ];

        return $this->parseJSON('json', [self::API_GET_LOGIN_INFO, $params]);
    }

    /**
     * {@inheritdoc}.
     */
    protected function getAuthUrl($state = null)
    {
        $params = array_merge([
            'corp_id'      => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'state'        => $state ?: md5(time()),
        ], ['usertype' => 'admin'], $this->parameters);

        $query = http_build_query($params, '', '&', $this->encodingType);

        return self::LOGIN_URL.'?'.$query;
    }
}
