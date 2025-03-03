<?php

use Illuminate\Support\Facades\Route;
use Webkul\PaypalV2\Http\Controllers\SmartButtonV2Controller;
use Webkul\PaypalV2\Http\Controllers\AdvSmartButtonController;
use Webkul\PaypalV2\Http\Controllers\StandardController;

Route::group(['middleware' => ['web']], function () {
    // Route::prefix('paypal/standard')->group(function () {
    //     Route::get('/redirect', [StandardController::class, 'redirect'])->name('paypal.standard.redirect');

    //     Route::get('/success', [StandardController::class, 'success'])->name('paypal.standard.success');

    //     Route::get('/cancel', [StandardController::class, 'cancel'])->name('paypal.standard.cancel');
    // });

    Route::prefix('paypal/smart-button-v2')->group(function () {
        Route::post('/index', [SmartButtonV2Controller::class, 'index'])->name('paypal.smart-button-v2.index');
        Route::get('/create-order', [SmartButtonV2Controller::class, 'createOrder'])->name('paypal.smart-button-v2.create-order');

        Route::post('/capture-order', [SmartButtonV2Controller::class, 'captureOrder'])->name('paypal.smart-button-v2.capture-order');
    });
    // Route::prefix('paypal/adv-smart-button')->group(function () {
    //     Route::post('/create-order', [AdvSmartButtonController::class, 'createOrder'])->name('paypal.adv-smart-button.create-order');

    //     Route::post('/capture-order', [AdvSmartButtonController::class, 'captureOrder'])->name('paypal.adv-smart-button.capture-order');
    // });
});

// Route::post('paypal/standard/ipn', [StandardController::class, 'ipn'])
//     ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
//     ->name('paypal.standard.ipn');
