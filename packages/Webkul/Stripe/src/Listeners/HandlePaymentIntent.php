<?php

namespace Webkul\Stripe\Listeners;

use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Stripe\Payment\SmartButton;

class HandlePaymentIntent
{
    /**
     * Create a new listener instance.
     *
     * @return void
     */
    public function __construct(
        protected SmartButton $smartButton,
        protected OrderRepository $orderRepository
    ) {}

    /**
     * Handle the payment intent data for online payment.
     *
     * @param  $paymentIntent
     * @return void
     */
    public function handlePaymentIntent($paymentIntent)
    {
        // TODO: When the status of payment intent is successed, you should do webhooking
    }
}
