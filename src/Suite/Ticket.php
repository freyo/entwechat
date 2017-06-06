<?php

namespace EntWeChat\Suite;

use Doctrine\Common\Cache\Cache;
use EntWeChat\Core\Exceptions\RuntimeException;

class Ticket
{
    /**
     * Cache manager.
     *
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache;

    /**
     * Suite Id.
     *
     * @var string
     */
    protected $suiteId;
    /**
     * Cache key prefix.
     *
     * @var string
     */
    protected $prefix = 'EntWeChat.suite.suite_ticket.';
    /**
     * Cache Key.
     *
     * @var string
     */
    private $cacheKey;

    /**
     * Ticket constructor.
     *
     * @param string                       $suiteId
     * @param \Doctrine\Common\Cache\Cache $cache
     */
    public function __construct($suiteId, Cache $cache)
    {
        $this->suiteId = $suiteId;
        $this->cache = $cache;
    }

    /**
     * Set component verify ticket to the cache.
     *
     * @param string $ticket
     *
     * @return bool
     */
    public function setTicket($ticket)
    {
        return $this->cache->save($this->getCacheKey(), $ticket);
    }

    /**
     * Get component verify ticket cache key.
     *
     * @return string $this->cacheKey
     */
    public function getCacheKey()
    {
        if (is_null($this->cacheKey)) {
            return $this->prefix.$this->suiteId;
        }

        return $this->cacheKey;
    }

    /**
     * Set component verify ticket cache key.
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
     * Get component verify ticket.
     *
     * @throws \EntWeChat\Core\Exceptions\RuntimeException
     *
     * @return string
     */
    public function getTicket()
    {
        if ($cached = $this->cache->fetch($this->getCacheKey())) {
            return $cached;
        }

        throw new RuntimeException('Suite ticket does not exists.');
    }
}
