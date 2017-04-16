<?php

namespace EntWeChat\Foundation\ServiceProviders;

use EntWeChat\Agent\Agent;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class AgentServiceProvider.
 */
class AgentServiceProvider implements ServiceProviderInterface
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
        $pimple['agent'] = function ($pimple) {
            return new Agent($pimple['access_token']);
        };
    }
}
