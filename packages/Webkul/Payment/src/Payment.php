<?php

namespace Webkul\Payment;

use Illuminate\Support\Facades\Config;
use Laravel\Cashier\Cashier;

class Payment
{
    /**
     * Returns all supported payment methods
     *
     * @return array
     */
    public function getSupportedPaymentMethods()
    {
        return [
            'payment_methods' => $this->getPaymentMethods(),
        ];
    }

    /**
     * Returns all supported payment methods
     *
     * @return array
     */
    public function getPaymentMethods($customer = null)
    {
        $paymentMethods = [];

        foreach (Config::get('payment_methods') as $paymentMethodConfig) {
            $paymentMethod = app($paymentMethodConfig['class']);

            if ($paymentMethod->isAvailable()) {
                $paymentMethods[] = [
                    'method'       => $paymentMethod->getCode(),
                    'method_title' => $paymentMethod->getTitle(),
                    'description'  => $paymentMethod->getDescription(),
                    'sort'         => $paymentMethod->getSortOrder(),
                    'image'        => $paymentMethod->getImage(),
                    'registered_payment_methods' => $this->getPaymentMethodsForCustomer($customer),
                ];
            }
        }

        usort($paymentMethods, function ($a, $b) {
            if ($a['sort'] == $b['sort']) {
                return 0;
            }

            return ($a['sort'] < $b['sort']) ? -1 : 1;
        });

        return $paymentMethods;
    }

    /**
     * Returns all supported payment methods account for customer
     *
     * @return array
     */
    public function getPaymentMethodsForCustomer($customer)
    {
        $registeredPaymentMethods = null;

        if (! $customer) {
            return $registeredPaymentMethods;
        }

        // Check stripe id & create stripe customer
        $stripeCustomer = Cashier::findBillable($customer->stripe_id);
        if (! $stripeCustomer) {
            $stripeCustomer =  $customer->createOrGetStripeCustomer();
        }

        // If stripe is not registered message
        if (! $stripeCustomer->hasPaymentMethod()) {
            $registeredPaymentMethods['message'] = 'No stripe payment method found';
        }
        // Get the stripe payment methods
        $registeredPaymentMethods = $stripeCustomer->paymentMethods();

        return $registeredPaymentMethods;
    }

    /**
     * Returns payment redirect url if have any
     *
     * @param  \Webkul\Checkout\Contracts\Cart  $cart
     * @return string
     */
    public function getRedirectUrl($cart)
    {
        $payment = app(Config::get('payment_methods.'.$cart->payment->method.'.class'));

        return $payment->getRedirectUrl();
    }

    /**
     * Returns payment method additional information
     *
     * @param  string  $code
     * @return array
     */
    public static function getAdditionalDetails($code)
    {
        $paymentMethodClass = app(Config::get('payment_methods.'.$code.'.class'));

        return $paymentMethodClass->getAdditionalDetails();
    }
}
