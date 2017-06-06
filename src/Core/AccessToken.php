<?php

namespace EntWeChat\Core;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;
use EntWeChat\Core\Exceptions\HttpException;

/**
 * Class AccessToken.
 */
class AccessToken
{
    /**
     * ID.
     *
     * @var string
     */
    protected $id;

    /**
     * Secret.
     *
     * @var string
     */
    protected $secret;

    /**
     * Cache.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Cache Key.
     *
     * @var string
     */
    protected $cacheKey;

    /**
     * Http instance.
     *
     * @var Http
     */
    protected $http;

    /**
     * Query name.
     *
     * @var string
     */
    protected $queryName = 'access_token';

    /**
     * Response Json key name.
     *
     * @var string
     */
    protected $tokenJsonKey = 'access_token';

    /**
     * Cache key prefix.
     *
     * @var string
     */
    protected $prefix = 'entwechat.common.access_token.';

    // API
    const API_TOKEN_GET = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken';

    /**
     * Constructor.
     *
     * @param string                       $id
     * @param string                       $secret
     * @param \Doctrine\Common\Cache\Cache $cache
     */
    public function __construct($id, $secret, Cache $cache = null)
    {
        $this->id = $id;
        $this->secret = $secret;
        $this->cache = $cache;
    }

    /**
     * Get token from WeChat API.
     *
     * @param bool $forceRefresh
     *
     * @return string
     */
    public function getToken($forceRefresh = false)
    {
        $cacheKey = $this->getCacheKey();
        $cached = $this->getCache()->fetch($cacheKey);

        if ($forceRefresh || empty($cached)) {
            $token = $this->getTokenFromServer();

            // XXX: T_T... 7200 - 1500
            $this->getCache()->save($cacheKey, $token[$this->tokenJsonKey], $token['expires_in'] - 1500);

            return $token[$this->tokenJsonKey];
        }

        return $cached;
    }

    /**
     * Set custom token.
     *
     * @param string $token
     * @param int    $expires
     *
     * @return $this
     */
    public function setToken($token, $expires = 7200)
    {
        $this->getCache()->save($this->getCacheKey(), $token, $expires - 1500);

        return $this;
    }

    /**
     * Return the id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return the app id.
     *
     * @return string
     */
    public function getAppId()
    {
        return $this->getId();
    }

    /**
     * Return the corp id.
     *
     * @return string
     */
    public function getCorpId()
    {
        return $this->getId();
    }

    /**
     * Return the secret.
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Return the fingerprint.
     *
     * @return string
     */
    public function getFingerprint()
    {
        return sha1($this->id.'|'.$this->secret);
    }

    /**
     * Set cache instance.
     *
     * @param \Doctrine\Common\Cache\Cache $cache
     *
     * @return AccessToken
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Return the cache manager.
     *
     * @return \Doctrine\Common\Cache\Cache
     */
    public function getCache()
    {
        return $this->cache ?: $this->cache = new FilesystemCache(sys_get_temp_dir());
    }

    /**
     * Set the query name.
     *
     * @param string $queryName
     *
     * @return $this
     */
    public function setQueryName($queryName)
    {
        $this->queryName = $queryName;

        return $this;
    }

    /**
     * Return the query name.
     *
     * @return string
     */
    public function getQueryName()
    {
        return $this->queryName;
    }

    /**
     * Return the API request queries.
     *
     * @return array
     */
    public function getQueryFields()
    {
        return [$this->queryName => $this->getToken()];
    }

    /**
     * Get the access token from WeChat server.
     *
     * @throws \EntWeChat\Core\Exceptions\HttpException
     *
     * @return string
     */
    public function getTokenFromServer()
    {
        $params = [
            'corpid'     => $this->id,
            'corpsecret' => $this->secret,
        ];

        $http = $this->getHttp();

        $token = $http->parseJSON($http->get(self::API_TOKEN_GET, $params));

        if (empty($token[$this->tokenJsonKey])) {
            throw new HttpException('Request AccessToken fail. response: '.json_encode($token, JSON_UNESCAPED_UNICODE));
        }

        return $token;
    }

    /**
     * Return the http instance.
     *
     * @return \EntWeChat\Core\Http
     */
    public function getHttp()
    {
        return $this->http ?: $this->http = new Http();
    }

    /**
     * Set the http instance.
     *
     * @param \EntWeChat\Core\Http $http
     *
     * @return $this
     */
    public function setHttp(Http $http)
    {
        $this->http = $http;

        return $this;
    }

    /**
     * Set the access token prefix.
     *
     * @param string $prefix
     *
     * @return $this
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Set access token cache key.
     *
     * @param string $cacheKey
     *
     * @return $this
     */
    public function setCacheKey($cacheKey)
    {
        $this->cacheKey = $cacheKey;

        return $this;
    }

    /**
     * Get access token cache key.
     *
     * @return string $this->cacheKey
     */
    public function getCacheKey()
    {
        if (is_null($this->cacheKey)) {
            return $this->prefix.$this->id;
        }

        return $this->cacheKey;
    }
}
