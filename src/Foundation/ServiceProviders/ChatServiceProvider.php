<?php

namespace EntWeChat\Foundation\ServiceProviders;

use EntWeChat\Chat\Chat;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ChatServiceProvider.
 */
class ChatServiceProvider implements ServiceProviderInterface
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
        $pimple['chat'] = function ($pimple) {
            return new Chat($pimple['access_token']);
        };
    }
}
