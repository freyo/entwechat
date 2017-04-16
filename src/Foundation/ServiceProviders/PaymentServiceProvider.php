<?php

namespace EntWeChat\Foundation\ServiceProviders;

use EntWeChat\Payment\CashCoupon\CashCoupon;
use EntWeChat\Payment\LuckyMoney\LuckyMoney;
use EntWeChat\Payment\Merchant;
use EntWeChat\Payment\MerchantPay\MerchantPay;
use EntWeChat\Payment\Payment;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class PaymentServiceProvider.
 */
class PaymentServiceProvider implements ServiceProviderInterface
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
        $pimple['merchant'] = function ($pimple) {
            $config = array_merge(
                ['app_id' => $pimple['config']['app_id']],
                $pimple['config']->get('payment', [])
            );

            return new Merchant($config);
        };

        $pimple['payment'] = function ($pimple) {
            return new Payment($pimple['merchant']);
        };

        $pimple['lucky_money'] = function ($pimple) {
            return new LuckyMoney($pimple['merchant']);
        };

        $pimple['merchant_pay'] = function ($pimple) {
            return new MerchantPay($pimple['merchant']);
        };

        $pimple['cash_coupon'] = function ($pimple) {
            return new CashCoupon($pimple['merchant']);
        };
    }
}
