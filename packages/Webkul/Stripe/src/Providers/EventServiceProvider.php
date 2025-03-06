<?php

namespace Webkul\Stripe\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Events\WebhookReceived;
use Webkul\Theme\ViewRenderEventManager;
use Webkul\Stripe\Listeners\HandlePaymentIntent;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen('bagisto.shop.layout.body.after', static function (ViewRenderEventManager $viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('stripe::checkout.onepage.stripe-smart-button');
        });

        // Event::listen('bagisto.shop.layout.body.after', static function (ViewRenderEventManager $viewRenderEventManager) {
        //     $viewRenderEventManager->addTemplate('stripe::checkout.onepage.stripe-checkout-session');
        // });

        Event::listen('sales.invoice.save.after', 'Webkul\Stripe\Listeners\Transaction@saveTransaction');

        Event::listen(WebhookReceived::class, [
            HandlePaymentIntent::class, 'handle'
        ]);
    }
}
