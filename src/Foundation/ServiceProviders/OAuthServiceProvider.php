<?php

namespace EntWeChat\Foundation\ServiceProviders;

use EntWeChat\Auth\App;
use EntWeChat\Auth\Web;
use EntWeChat\Auth\WorkWeb;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class OAuthServiceProvider.
 */
class OAuthServiceProvider implements ServiceProviderInterface
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
        $pimple['oauth'] = function ($pimple) {
            return new App($pimple['access_token']);
        };

        $pimple['auth'] = function ($pimple) {
            return new Web($pimple['access_token']);
        };

        $auth_work = function ($pimple) {
            return new WorkWeb($pimple['access_token']);
        };

        $pimple['auth_work'] = $auth_work;
        $pimple['auth.work'] = $auth_work;
    }
}
