<?php

namespace EntWeChat\Auth;

use EntWeChat\Core\Exceptions\InvalidStateException;

/**
 * Class App.
 */
class App extends AbstractAuthentication
{
    const AUTH_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize';
    const API_GET_USER_INFO = 'https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo';
    const API_GET_USER_DETAIL = 'https://qyapi.weixin.qq.com/cgi-bin/user/getuserdetail';

    /**
     * Indicates if the session state should be utilized.
     *
     * @var bool
     */
    protected $stateless = true;

    /**
     * {@inheritdoc}.
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(self::AUTH_URL, $state);
    }

    /**
     * {@inheritdoc}.
     */
    protected function buildAuthUrlFromBase($url, $state)
    {
        $query = http_build_query($this->getCodeFields($state), '', '&', $this->encodingType);

        return $url.'?'.$query.'#wechat_redirect';
    }

    /**
     * {@inheritdoc}.
     */
    protected function getCodeFields($state = null)
    {
        return array_merge([
            'appid'         => $this->clientId,
            'redirect_uri'  => $this->redirectUrl,
            'response_type' => 'code',
            'scope'         => $this->formatScopes($this->scopes, $this->scopeSeparator),
            'state'         => $state ?: md5(time()),
        ], $this->parameters);
    }

    /**
     * @param null $code
     *
     * @throws InvalidStateException
     *
     * @return \EntWeChat\Support\Collection
     */
    public function user($code = null)
    {
        if ($this->hasInvalidState()) {
            throw new InvalidStateException();
        }

        $params = [
            'code' => $code ?: $this->request->get('code'),
        ];

        return $this->parseJSON('get', [self::API_GET_USER_INFO, $params]);
    }

    /**
     * @param $user_ticket
     *
     * @return \EntWeChat\Support\Collection
     */
    public function detail($user_ticket)
    {
        $params = [
            'user_ticket' => $user_ticket,
        ];

        return $this->parseJSON('post', [self::API_GET_USER_DETAIL, $params]);
    }
}
