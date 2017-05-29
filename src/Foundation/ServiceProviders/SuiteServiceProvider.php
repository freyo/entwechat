<?php

namespace EntWeChat\Foundation\ServiceProviders;

use EntWeChat\Auth\App;
use EntWeChat\Encryption\Encryptor;
use EntWeChat\Foundation\Application;
use EntWeChat\Suite\AccessToken;
use EntWeChat\Suite\Api\BaseApi;
use EntWeChat\Suite\Api\PreAuthorization;
use EntWeChat\Suite\Authorization;
use EntWeChat\Suite\AuthorizerAccessToken;
use EntWeChat\Suite\EventHandlers;
use EntWeChat\Suite\Guard;
use EntWeChat\Suite\Suite;
use EntWeChat\Suite\Ticket;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class SuiteServiceProvider implements ServiceProviderInterface
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
        $pimple['suite.ticket'] = function ($pimple) {
            return new Ticket(
                $pimple['config']['suite']['suite_id'],
                $pimple['cache']
            );
        };

        $pimple['suite.access_token'] = function ($pimple) {
            $accessToken = new AccessToken(
                $pimple['config']['suite']['suite_id'],
                $pimple['config']['suite']['secret'],
                $pimple['cache']
            );
            $accessToken->setTicket($pimple['suite.ticket']);

            return $accessToken;
        };

        $pimple['suite.encryptor'] = function ($pimple) {
            return new Encryptor(
                $pimple['config']['suite']['suite_id'],
                $pimple['config']['suite']['token'],
                $pimple['config']['suite']['aes_key']
            );
        };

        $pimple['suite'] = function ($pimple) {
            return new Suite($pimple);
        };

        $pimple['suite.server'] = function ($pimple) {
            $server = new Guard($pimple['config']['suite']['token']);
            $server->debug($pimple['config']['debug']);
            $server->setEncryptor($pimple['suite.encryptor']);
            $server->setHandlers([
                Guard::EVENT_CREATE_AUTH => $pimple['suite.handlers.create_auth'],
                Guard::EVENT_CANCEL_AUTH => $pimple['suite.handlers.cancel_auth'],
                Guard::EVENT_CHANGE_AUTH => $pimple['suite.handlers.change_auth'],
                Guard::EVENT_SUITE_TICKET => $pimple['suite.handlers.suite_ticket'],
            ]);

            return $server;
        };

        $pimple['suite.pre_auth'] = $pimple['suite.pre_authorization'] = function ($pimple) {
            return new PreAuthorization(
                $pimple['suite.access_token'],
                $pimple['request']
            );
        };

        $pimple['suite.api'] = function ($pimple) {
            return new BaseApi(
                $pimple['suite.access_token'],
                $pimple['request']
            );
        };

        $pimple['suite.authorization'] = function ($pimple) {
            return new Authorization(
                $pimple['suite.api'],
                $pimple['config']['suite']['corp_id'],
                $pimple['cache']
            );
        };

        $pimple['suite.authorizer_access_token'] = function ($pimple) {
            return new AuthorizerAccessToken(
                $pimple['config']['suite']['corp_id'],
                $pimple['suite.authorization']
            );
        };

        // Authorization events handlers.
        $pimple['suite.handlers.suite_ticket'] = function ($pimple) {
            return new EventHandlers\SuiteTicket($pimple['suite.ticket']);
        };
        $pimple['suite.handlers.create_auth'] = function () {
            return new EventHandlers\CreateAuth();
        };
        $pimple['suite.handlers.change_auth'] = function () {
            return new EventHandlers\ChangeAuth();
        };
        $pimple['suite.handlers.cancel_auth'] = function () {
            return new EventHandlers\CancelAuth();
        };

        $pimple['suite.app'] = function ($pimple) {
            return new Application($pimple['config']->toArray());
        };

        // OAuth for Suite.
        $pimple['suite.oauth'] = function ($pimple) {
            return new App($pimple['suite.authorizer_access_token']->getToken());
        };
    }

    /**
     * Prepare the OAuth callback url for wechat.
     *
     * @param Container $pimple
     *
     * @return string
     */
    private function prepareCallbackUrl($pimple)
    {
        $callback = $pimple['config']->get('oauth.callback');
        if (0 === stripos($callback, 'http')) {
            return $callback;
        }
        $baseUrl = $pimple['request']->getSchemeAndHttpHost();

        return $baseUrl.'/'.ltrim($callback, '/');
    }
}
