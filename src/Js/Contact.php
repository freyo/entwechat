<?php

namespace EntWeChat\Js;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;
use EntWeChat\Core\AbstractAPI;
use EntWeChat\Support\Str;
use EntWeChat\Support\Url as UrlHelper;

/**
 * Class Contact.
 */
class Contact extends AbstractAPI
{
    /**
     * Cache.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Current URI.
     *
     * @var string
     */
    protected $url;

    /**
     * Group ticket cache prefix.
     */
    const GROUP_TICKET_CACHE_PREFIX = 'entwechat.group_ticket.';

    /**
     * Api of group ticket.
     */
    const API_GROUP_TICKET = 'https://qyapi.weixin.qq.com/cgi-bin/ticket/get';

    /**
     * Get config json for jsapi.
     *
     * @param array $params
     * @param bool  $json
     *
     * @return array|string
     */
    public function config(array $params, $json = true)
    {
        $signPackage = $this->signature();

        $config = array_merge($signPackage, ['params' => $params]);

        return $json ? json_encode($config) : $config;
    }

    /**
     * Return jsapi config as a PHP array.
     *
     * @param array $params
     *
     * @return array
     */
    public function getConfigArray(array $params)
    {
        return $this->config($params, false);
    }

    /**
     * Get ticket.
     *
     * @param bool $forceRefresh
     *
     * @return string
     */
    public function ticket($forceRefresh = false)
    {
        $key = self::GROUP_TICKET_CACHE_PREFIX.$this->getAccessToken()->getFingerprint();
        $ticket = $this->getCache()->fetch($key);

        if (!$forceRefresh && !empty($ticket)) {
            return $ticket;
        }

        $result = $this->parseJSON('get', [self::API_GROUP_TICKET, ['type' => 'contact']]);

        $this->getCache()->save($key, $result, $result['expires_in'] - 500);

        return $result;
    }

    /**
     * Build signature.
     *
     * @param string $url
     * @param string $nonce
     * @param int    $timestamp
     *
     * @return array
     */
    public function signature($url = null, $nonce = null, $timestamp = null)
    {
        $url = $url ? $url : $this->getUrl();
        $nonce = $nonce ? $nonce : Str::quickRandom(10);
        $timestamp = $timestamp ? $timestamp : time();
        $ticket = $this->ticket();

        $sign = [
            'groupId'   => $ticket['group_id'],
            'nonceStr'  => $nonce,
            'timestamp' => $timestamp,
            'signature' => $this->getSignature($ticket['ticket'], $nonce, $timestamp, $url),
        ];

        return $sign;
    }

    /**
     * Sign the params.
     *
     * @param string $ticket
     * @param string $nonce
     * @param int    $timestamp
     * @param string $url
     *
     * @return string
     */
    public function getSignature($ticket, $nonce, $timestamp, $url)
    {
        return sha1("group_ticket={$ticket}&noncestr={$nonce}&timestamp={$timestamp}&url={$url}");
    }

    /**
     * Set current url.
     *
     * @param string $url
     *
     * @return Contact
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get current url.
     *
     * @return string
     */
    public function getUrl()
    {
        if ($this->url) {
            return $this->url;
        }

        return UrlHelper::current();
    }

    /**
     * Set cache manager.
     *
     * @param \Doctrine\Common\Cache\Cache $cache
     *
     * @return $this
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Return cache manager.
     *
     * @return \Doctrine\Common\Cache\Cache
     */
    public function getCache()
    {
        return $this->cache ?: $this->cache = new FilesystemCache(sys_get_temp_dir());
    }
}
