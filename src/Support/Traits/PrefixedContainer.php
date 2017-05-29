<?php

namespace EntWeChat\Support\Traits;

use EntWeChat\Support\Str;
use Pimple\Container;

/**
 * Trait PrefixedContainer.
 */
trait PrefixedContainer
{
    /**
     * Container.
     *
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * ContainerAccess constructor.
     *
     * @param \Pimple\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Fetches from pimple container.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function fetch($key)
    {
        return $this->$key;
    }

    /**
     * Gets a parameter or an object from pimple container.
     *
     * Get the `class basename` of the current class.
     * Convert `class basename` to snake-case and concatenation with dot notation.
     *
     * E.g. Class 'EntWechat', $key foo -> 'ent_wechat.foo'
     *
     * @param string $key The unique identifier for the parameter or object
     *
     * @throws \InvalidArgumentException If the identifier is not defined
     *
     * @return mixed The value of the parameter or an object
     */
    public function __get($key)
    {
        $className = basename(str_replace('\\', '/', static::class));

        $name = Str::snake($className) . '.' . $key;

        return $this->container->offsetGet($name);
    }
}
