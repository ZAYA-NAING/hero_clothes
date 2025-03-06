<?php

namespace Webkul\Stripe\Listeners;

use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;
use Webkul\Stripe\Payment\HandleCheckoutSessionCompleted;

class StripeEventListener
{
    /**
     * Handle received Stripe webhooks.
     */
    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] === 'payment_intent.payment_succeeded') {
            // Handle the incoming event...
            Log::info('WebhookReceived:' . $event);
            $pi = $event->payload['data']['object'];
            Log::info('WebhookReceived:' . $pi);
        } else if ($event->payload['type'] === 'checkout.session.completed') {
            (new HandleCheckoutSessionCompleted)->handle($event->payload['data']['object']['id']);
        }
    }
}
