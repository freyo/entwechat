<?php

namespace EntWeChat\Foundation\ServiceProviders;

use EntWeChat\User\Department;
use EntWeChat\User\Tag;
use EntWeChat\User\User;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class UserServiceProvider.
 */
class UserServiceProvider implements ServiceProviderInterface
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
        $pimple['user'] = function ($pimple) {
            return new User($pimple['access_token']);
        };

        $department = function ($pimple) {
            return new Department($pimple['access_token']);
        };

        $tag = function ($pimple) {
            return new Tag($pimple['access_token']);
        };

        $pimple['user_department'] = $department;
        $pimple['user.department'] = $department;

        $pimple['user_tag'] = $tag;
        $pimple['user.tag'] = $tag;
    }
}
