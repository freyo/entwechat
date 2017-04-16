<?php

namespace EntWeChat\Foundation\ServiceProviders;

use EntWeChat\Js\Contact;
use EntWeChat\Js\Js;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class JsServiceProvider.
 */
class JsServiceProvider implements ServiceProviderInterface
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
        $pimple['js'] = function ($pimple) {
            $js = new Js($pimple['access_token']);
            $js->setCache($pimple['cache']);

            return $js;
        };

        $contact = function ($pimple) {
            $js = new Contact($pimple['access_token']);
            $js->setCache($pimple['cache']);

            return $js;
        };

        $pimple['js_contact'] = $contact;
        $pimple['js.contact'] = $contact;
    }
}
