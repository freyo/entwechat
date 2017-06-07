<?php

namespace EntWeChat\Foundation\ServiceProviders;

use EntWeChat\OA\API;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class OAServiceProvider.
 */
class OAServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $pimple)
    {
        $pimple['oa'] = function ($pimple) {
            return new API($pimple['access_token']);
        };
    }
}
