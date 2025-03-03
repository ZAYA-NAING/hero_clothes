<?php

namespace Webkul\Stripe\Listeners;

use Webkul\Stripe\Payment\SmartButton;
use Webkul\Sales\Repositories\OrderTransactionRepository;

class HandlePaymentIntent
{
    /**
     * Create a new listener instance.
     *
     * @return void
     */
    public function __construct(
        protected SmartButton $smartButton,
        protected OrderTransactionRepository $orderTransactionRepository
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
