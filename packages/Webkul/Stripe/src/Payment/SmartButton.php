<?php

namespace Webkul\Stripe\Payment;

use Laravel\Cashier\Cashier;

class SmartButton extends Stripe
{
    /**
     * Payment method code.
     *
     * @var string
     */
    protected $code = 'stripe_smart_button';

    /**
     * Payment intent.
     *
     * @var
     */
    protected $paymentIntent;

    /**
     * Paypal partner attribution id.
     *
     * @var string
     */
    protected $paypalPartnerAttributionId = 'Bagisto_Cart';

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Creat customer and a new stripe billable instance
     *
     * @return \Laravel\Cashier\Billable|null
     */
    public function getStripeCustomer()
    {
        if (! auth()->guard('customer')->check()) {
            return redirect()->route('shop.customer.session.index');
        }

        $customer = auth()->guard('customer')->user();

        $customer->createOrGetStripeCustomer();

        return Cashier::findBillable($customer->stripe_id);
    }

    /**
     * Create the payment intent for client.
     *
     * @param  array $amount
     * @return \Stripe\PaymentIntent
     */
    public function createPaymentIntent($amount) {
        $this->paymentIntent = $this->client()->paymentIntents->create([
            'amount'   => $amount['value'], // Amount in cents
            'currency' => $amount['currency_code'],
        ]);

        return $this->paymentIntent;
    }

    /**
     * Confirm the payment for client.
     *
     * @param  string $paymentMethodId
     * @return mixed
     */
    public function confirmPayment($paymentMethodId) {
        $payment = $this->client()->paymentIntents->confirm(
            $this->paymentIntent->id,
            ['payment_method' => $paymentMethodId]
        );
        return $payment;
    }


    /**
     * Return paypal redirect url
     *
     * @return string
     */
    public function getRedirectUrl() {}

}
