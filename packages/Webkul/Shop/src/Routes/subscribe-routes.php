<?php

use Illuminate\Support\Facades\Route;
use Webkul\Shop\Http\Controllers\StripeSubscriptionController;

Route::controller(StripeSubscriptionController::class)->prefix('checkout/stripe')->group(function () {
    Route::get('', 'index')->name('shop.checkout.stripe.index');
    Route::post('/single-charge', 'singleCharge')->name('shop.checkout.stripe.single.charge');

    Route::get('/subscribe', 'showSubscription')->name('shop.checkout.stripe.subscribe');
    Route::post('/subscribe', 'processSubscription')->name('shop.checkout.stripe.store');
    // welcome page only for subscribed users
    Route::get('/welcome', 'showWelcome')->middleware('subscribed');
});


