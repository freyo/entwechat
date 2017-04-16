<?php

namespace EntWeChat\Foundation\ServiceProviders;

use EntWeChat\ShakeAround\ShakeAround;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ShakeAroundServiceProvider.
 */
class ShakeAroundServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple['shakearound'] = function ($pimple) {
            return new ShakeAround($pimple['access_token']);
        };
    }
}
