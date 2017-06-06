<?php

namespace EntWeChat\Suite;

use Doctrine\Common\Cache\Cache;
use EntWeChat\Core\Exception;
use EntWeChat\Suite\Api\BaseApi;

class Authorization
{
    const CACHE_KEY_ACCESS_TOKEN = 'EntWeChat.suite.authorizer_access_token';
    const CACHE_KEY_PERMANENT_CODE = 'EntWeChat.suite.authorizer_permanent_code';

    /**
     * Cache.
     *
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache;

    /**
     * Base API.
     *
     * @var \EntWeChat\Suite\Api\BaseApi
     */
    protected $api;

    /**
     * Suite Id.
     *
     * @var string
     */
    protected $suiteId;

    /**
     * Authorizer Corp Id.
     *
     * @var string
     */
    protected $authorizerCorpId;

    /**
     * Authorization Constructor.
     *
     * @param \EntWeChat\Suite\Api\BaseApi $api
     * @param string                       $suiteId
     * @param \Doctrine\Common\Cache\Cache $cache
     */
    public function __construct(BaseApi $api, $suiteId, Cache $cache)
    {
        $this->api = $api;
        $this->suiteId = $suiteId;
        $this->cache = $cache;
    }

    /**
     * Gets the base api.
     *
     * @return \EntWeChat\Suite\Api\BaseApi
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * Saves the authorizer access token in cache.
     *
     * @param string $token
     *
     * @return bool TRUE if the entry was successfully stored in the cache, FALSE otherwise
     */
    public function setAuthorizerAccessToken($token, $expires = 7200)
    {
        return $this->cache->save($this->getAuthorizerAccessTokenKey(), $token, $expires);
    }

    /**
     * Gets the authorizer access token cache key.
     *
     * @return string
     */
    public function getAuthorizerAccessTokenKey()
    {
        return self::CACHE_KEY_ACCESS_TOKEN.$this->suiteId.$this->getAuthorizerCorpId();
    }

    /**
     * Gets the authorizer corp id, or throws if not found.
     *
     * @throws \EntWeChat\Core\Exception
     *
     * @return string
     */
    public function getAuthorizerCorpId()
    {
        if (!$this->authorizerCorpId) {
            throw new Exception(
                'Authorizer Corp Id is not present, you may not make the authorization yet.'
            );
        }

        return $this->authorizerCorpId;
    }

    /**
     * Sets the authorizer corp id.
     *
     * @param string $authorizerCorpId
     *
     * @return $this
     */
    public function setAuthorizerCorpId($authorizerCorpId)
    {
        $this->authorizerCorpId = $authorizerCorpId;

        return $this;
    }

    /**
     * Gets the authorizer access token.
     *
     * @return string
     */
    public function getAuthorizerAccessToken()
    {
        return $this->cache->fetch($this->getAuthorizerAccessTokenKey());
    }

    /**
     * Saves the authorizer refresh token in cache.
     *
     * @param string $permanentCode
     *
     * @return bool TRUE if the entry was successfully stored in the cache, FALSE otherwise
     */
    public function setAuthorizerPermanentCode($permanentCode)
    {
        return $this->cache->save($this->getAuthorizerPermanentCodeKey(), $permanentCode);
    }

    /**
     * Gets the authorizer refresh token cache key.
     *
     * @return string
     */
    public function getAuthorizerPermanentCodeKey()
    {
        return self::CACHE_KEY_PERMANENT_CODE.$this->suiteId.$this->getAuthorizerCorpId();
    }

    /**
     * Gets the authorizer refresh token.
     *
     * @throws \EntWeChat\Core\Exception when refresh token is not present
     *
     * @return string
     */
    public function getAuthorizerPermanentCode()
    {
        if ($permanentCode = $this->cache->fetch($this->getAuthorizerPermanentCodeKey())) {
            return $permanentCode;
        }

        throw new Exception(
            'Authorizer Permanent Code is not present, you may not make the authorization yet.'
        );
    }

    /**
     * Removes the authorizer access token from cache.
     *
     * @return bool TRUE if the cache entry was successfully deleted, FALSE otherwise.
     *              Deleting a non-existing entry is considered successful
     */
    public function removeAuthorizerAccessToken()
    {
        return $this->cache->delete($this->getAuthorizerAccessTokenKey());
    }

    /**
     * Removes the authorizer refresh token from cache.
     *
     * @return bool TRUE if the cache entry was successfully deleted, FALSE otherwise.
     *              Deleting a non-existing entry is considered successful
     */
    public function removeAuthorizerPermanentCode()
    {
        return $this->cache->delete($this->getAuthorizerPermanentCodeKey());
    }
}
