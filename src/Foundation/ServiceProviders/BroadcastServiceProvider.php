<?php

namespace EntWeChat\Foundation\ServiceProviders;

use EntWeChat\Broadcast\Broadcast;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class BroadcastServiceProvider.
 */
class BroadcastServiceProvider implements ServiceProviderInterface
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
        $pimple['broadcast'] = function ($pimple) {
            return new Broadcast($pimple['access_token']);
        };
    }
}
