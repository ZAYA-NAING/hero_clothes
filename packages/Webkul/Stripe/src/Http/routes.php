<?php

use Illuminate\Support\Facades\Route;
use Webkul\Stripe\Http\Controllers\CheckoutSessionController;
use Webkul\Stripe\Http\Controllers\SmartButtonController;
use Webkul\Stripe\Http\Controllers\StandardController;

Route::group(['middleware' => ['web']], function () {
    Route::prefix('stripe/standard')->group(function () {
        Route::get('/redirect', [StandardController::class, 'redirect'])->name('stripe.standard.redirect');

        Route::get('/success', [StandardController::class, 'success'])->name('stripe.standard.success');

        Route::get('/cancel', [StandardController::class, 'cancel'])->name('stripe.standard.cancel');
    });

    Route::prefix('stripe/checkout-session')->group(function () {
        Route::get('/redirect', [CheckoutSessionController::class, 'redirect'])->name('stripe.checkout-session.redirect');

        Route::get('/create-order', [CheckoutSessionController::class, 'createOrder'])->name('stripe.checkout-session.create-order');

        Route::get('/success', [CheckoutSessionController::class, 'success'])->name('stripe.checkout-session.success');

        Route::get('/cancel', [CheckoutSessionController::class, 'cancel'])->name('stripe.checkout-session.cancel');

        Route::post('/webhook', [CheckoutSessionController::class, 'webhook'])->name('stripe.checkout-session.webhook');
    });

    Route::prefix('stripe/smart-button')->group(function () {
        Route::get('/get-payment-methods', [SmartButtonController::class, 'getPaymentMethods'])->name('stripe.smart-button.get-payment-methods');

        Route::post('/create-payment-method', [SmartButtonController::class, 'createPaymentMethod'])->name('stripe.smart-button.create-payment-method');

        Route::get('/has-payment-method', [SmartButtonController::class, 'hasPaymentMethod'])->name('stripe.smart-button.has-payment-method');

        Route::post('/pay', [SmartButtonController::class, 'payWith'])->name('stripe.smart-button.pay');

        Route::post('/handle-payment-intent', [SmartButtonController::class, 'handlePaymentIntent'])->name('stripe.smart-button.handle-payment-intent');

        Route::get('/webhook', [SmartButtonController::class, 'webhook'])->name('stripe.smart-button.webhook');

        Route::get('/stripe-webhook', [SmartButtonController::class, 'stripeWebhook'])->name('stripe.smart-button.stripe-webhook');

        Route::get('/create-payment', [SmartButtonController::class, 'handlePayment'])->name('stripe.smart-button.create-payment');

        Route::post('/handle-payment', [SmartButtonController::class, 'handlePayment'])->name('stripe.smart-button.handle-payment');

        Route::get('/create-order', [SmartButtonController::class, 'createOrder'])->name('stripe.smart-button.create-order');

        Route::post('/capture-order', [SmartButtonController::class, 'captureOrder'])->name('stripe.smart-button.capture-order');
    });
});


Route::post('stripe/standard/ipn', [StandardController::class, 'ipn'])
    ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
    ->name('stripe.standard.ipn');
