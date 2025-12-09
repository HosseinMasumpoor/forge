<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\app\Http\Controllers\V1\OrderController;

Route::prefix('v1')->group(function () {
    Route::middleware(['auth:user'])->group(function () {
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'list'])->name('orders.list');
            Route::post('/purchase', [OrderController::class, 'purchase'])->name('orders.purchase');
        });
    });

    Route::match(['get', 'post'], '/payment/verify', [OrderController::class, 'verify'])->name('payment.verify');
});
