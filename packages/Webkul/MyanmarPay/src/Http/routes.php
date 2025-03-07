<?php

use Illuminate\Support\Facades\Route;
use Webkul\Paypal\Http\Controllers\SmartButtonController;

Route::group(['middleware' => ['web']], function () {
    Route::prefix('myanmarpay/myanmarpay-manual')->group(function () {
        Route::get('/create-order', [SmartButtonController::class, 'createOrder'])->name('myanmarpay.myanmarpay-manual.create-order');

        // Route::post('/capture-order', [SmartButtonController::class, 'captureOrder'])->name('myanmarpay.myanmarpay-manual.capture-order');
    });
});
