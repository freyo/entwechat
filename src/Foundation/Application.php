<?php

namespace EntWeChat\Foundation;

use Doctrine\Common\Cache\Cache as CacheInterface;
use Doctrine\Common\Cache\FilesystemCache;
use EntWeChat\Core\AccessToken;
use EntWeChat\Core\Exceptions\InvalidConfigException;
use EntWeChat\Core\Http;
use EntWeChat\Support\Log;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Application.
 *
 * @property \EntWeChat\Core\AccessToken                $access_token
 * @property \EntWeChat\Server\Guard                    $server
 * @property \EntWeChat\User\User                       $user
 * @property \EntWeChat\User\Tag                        $user_tag
 * @property \EntWeChat\User\Department                 $user_department
 * @property \EntWeChat\User\Batch                      $user_batch
 * @property \EntWeChat\Js\Js                           $js
 * @property \EntWeChat\Js\Contact                      $js_contact
 * @property \EntWeChat\Menu\Menu                       $menu
 * @property \EntWeChat\Broadcast\Broadcast             $broadcast
 * @property \EntWeChat\Material\Material               $material
 * @property \EntWeChat\Material\Temporary              $material_temporary
 * @property \EntWeChat\Card\Card                       $card
 * @property \EntWeChat\Agent\Agent                     $agent
 * @property \EntWeChat\Chat\Chat                       $chat
 * @property \EntWeChat\ShakeAround\ShakeAround         $shakearound
 * @property \EntWeChat\Soter\Soter                     $soter
 * @property \EntWeChat\Payment\Merchant                $merchant
 * @property \EntWeChat\Payment\Payment                 $payment
 * @property \EntWeChat\Payment\LuckyMoney\LuckyMoney   $lucky_money
 * @property \EntWeChat\Payment\MerchantPay\MerchantPay $merchant_pay
 * @property \EntWeChat\Payment\CashCoupon\CashCoupon   $cash_coupon
 * @property \EntWeChat\Auth\App                        $oauth
 * @property \EntWeChat\Auth\Web                        $auth
 * @property \EntWeChat\Staff\Staff                     $staff
 * @property \EntWeChat\Suite\Suite                     $suite
 * @property \EntWeChat\OA\API                          $oa
 *
 * @method \EntWeChat\Support\Collection getCallbackIp()
 */
class Application extends Container
{
    /**
     * Service Providers.
     *
     * @var array
     */
    protected $providers = [
        ServiceProviders\ServerServiceProvider::class,
        ServiceProviders\UserServiceProvider::class,
        ServiceProviders\JsServiceProvider::class,
        ServiceProviders\MenuServiceProvider::class,
        ServiceProviders\BroadcastServiceProvider::class,
        ServiceProviders\CardServiceProvider::class,
        ServiceProviders\MaterialServiceProvider::class,
        ServiceProviders\AgentServiceProvider::class,
        ServiceProviders\ChatServiceProvider::class,
        ServiceProviders\SoterServiceProvider::class,
        ServiceProviders\OAuthServiceProvider::class,
        ServiceProviders\PaymentServiceProvider::class,
        ServiceProviders\ShakeAroundServiceProvider::class,
        ServiceProviders\StaffServiceProvider::class,
        ServiceProviders\SuiteServiceProvider::class,
        ServiceProviders\FundamentalServiceProvider::class,
        ServiceProviders\OAServiceProvider::class,
    ];

    /**
     * Account Instances.
     *
     * @var array
     */
    protected $accounts = [];

    /**
     * Application constructor.
     *
     * @param array $config
     */
    public function __construct($config)
    {
        parent::__construct();

        $this['config'] = function () use ($config) {
            return new Config($config);
        };

        if ($this['config']['debug']) {
            error_reporting(E_ALL);
        }

        $this->registerProviders();
        $this->registerBase();
        $this->initializeLogger();

        Http::setDefaultOptions($this['config']->get('guzzle', ['timeout' => 5.0]));

        $this->logConfiguration($config);
    }

    /**
     * Load account.
     *
     * @param string $account
     *
     * @throws InvalidConfigException
     *
     * @return Application
     */
    public function account($account)
    {
        if (isset($this->accounts[$account])) {
            return $this->accounts[$account];
        }

        if (!isset($this['config']['account'][$account])) {
            throw new InvalidConfigException('This account not exist.');
        }

        return $this->accounts[$account] = new self(
            array_merge($this['config']->all(), $this['config']['account'][$account])
        );
    }

    /**
     * Log configuration.
     *
     * @param array $config
     */
    public function logConfiguration($config)
    {
        $config = new Config($config);

        if ($config->has('account')) {
            $config->forget('account');
        }

        $keys = ['corp_id', 'secret', 'suite.suite_id', 'suite.secret'];
        foreach ($keys as $key) {
            !$config->has($key) || $config[$key] = '***'.substr($config[$key], -5);
        }

        Log::debug('Current config:', $config->toArray());
    }

    /**
     * Add a provider.
     *
     * @param string $provider
     *
     * @return Application
     */
    public function addProvider($provider)
    {
        array_push($this->providers, $provider);

        return $this;
    }

    /**
     * Set providers.
     *
     * @param array $providers
     */
    public function setProviders(array $providers)
    {
        $this->providers = [];

        foreach ($providers as $provider) {
            $this->addProvider($provider);
        }
    }

    /**
     * Return all providers.
     *
     * @return array
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * Magic get access.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function __get($id)
    {
        return $this->offsetGet($id);
    }

    /**
     * Magic set access.
     *
     * @param string $id
     * @param mixed  $value
     */
    public function __set($id, $value)
    {
        $this->offsetSet($id, $value);
    }

    /**
     * Register providers.
     */
    private function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }
    }

    /**
     * Register basic providers.
     */
    private function registerBase()
    {
        $this['request'] = function () {
            return Request::createFromGlobals();
        };

        if (!empty($this['config']['cache']) && $this['config']['cache'] instanceof CacheInterface) {
            $this['cache'] = $this['config']['cache'];
        } else {
            $this['cache'] = function () {
                return new FilesystemCache(sys_get_temp_dir());
            };
        }

        $this['access_token'] = function () {
            return new AccessToken(
                $this['config']['corp_id'],
                $this['config']['secret'],
                $this['cache']
            );
        };
    }

    /**
     * Initialize logger.
     */
    private function initializeLogger()
    {
        if (Log::hasLogger()) {
            return;
        }

        $logger = new Logger('entwechat');

        if (!$this['config']['debug'] || defined('PHPUNIT_RUNNING')) {
            $logger->pushHandler(new NullHandler());
        } elseif ($this['config']['log.handler'] instanceof HandlerInterface) {
            $logger->pushHandler($this['config']['log.handler']);
        } elseif ($logFile = $this['config']['log.file']) {
            $logger->pushHandler(new StreamHandler(
                    $logFile,
                    $this['config']->get('log.level', Logger::WARNING),
                    true,
                    $this['config']->get('log.permission', null))
            );
        }

        Log::setLogger($logger);
    }
}
