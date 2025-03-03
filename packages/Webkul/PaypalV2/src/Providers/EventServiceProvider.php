<?php

namespace Webkul\PaypalV2\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Webkul\Theme\ViewRenderEventManager;

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
            $viewRenderEventManager->addTemplate('paypal::checkout.onepage.paypal-smart-button-v2');
        });

        Event::listen('sales.invoice.save.after', 'Webkul\PaypalV2\Listeners\Transaction@saveTransaction');
    }
}
