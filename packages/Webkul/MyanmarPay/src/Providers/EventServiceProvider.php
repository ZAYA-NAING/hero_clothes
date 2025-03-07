<?php

namespace Webkul\MyanmarPay\Providers;

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
            $viewRenderEventManager->addTemplate('myanmarpay::checkout.onepage.myanmarpay-manual');
        });

        Event::listen('sales.invoice.save.after', 'Webkul\Paypal\Listeners\Transaction@saveTransaction');
    }
}
