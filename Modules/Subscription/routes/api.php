<?php

use Illuminate\Support\Facades\Route;
use Modules\Subscription\app\Http\Controllers\V1\PlanController;
use Modules\Subscription\app\Http\Controllers\V1\SubscriptionController;

Route::prefix('v1')->group(function () {
    Route::prefix('plans')->group(function () {
        Route::get('/', [PlanController::class, 'list'])->name('plans.list');
        Route::get('/{id}', [PlanController::class, 'show'])->name('plans.show');
        Route::get('/{planId}/offers', [PlanController::class, 'offers'])->name('plans.offers');
    });

    Route::middleware(['auth:user'])->group(function () {
        Route::prefix('subscriptions')->group(function () {
            Route::get('/current', [SubscriptionController::class, 'getCurrentSubscription'])->name('subscriptions.current');
            Route::get('/remaining', [SubscriptionController::class, 'getRemainingSubscription'])->name('subscriptions.remaining');
        });
    });
});
