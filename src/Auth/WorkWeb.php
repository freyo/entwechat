<?php

namespace EntWeChat\Auth;

use EntWeChat\Core\Exceptions\InvalidStateException;

/**
 * Class WorkWeb.
 */
class WorkWeb extends AbstractAuthentication
{
    const AUTH_URL = 'https://open.work.weixin.qq.com/wwopen/sso/qrConnect';
    const API_GET_USER_INFO = 'https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo';

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

        return $url.'?'.$query;
    }

    /**
     * {@inheritdoc}.
     */
    protected function getCodeFields($state = null)
    {
        return array_merge([
            'appid'         => $this->clientId,
            'redirect_uri'  => $this->redirectUrl,
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
}
