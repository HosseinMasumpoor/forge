<?php

use Illuminate\Support\Facades\Route;
use Modules\SocialSync\app\Http\Controllers\V1\GenerateController;
use Modules\SocialSync\app\Http\Controllers\V1\PostController;
use Modules\SocialSync\Http\Controllers\SocialSyncController;
use Modules\SocialSync\app\Http\Controllers\V1\SocialAccountController;

Route::prefix('v1')->group(function () {
    Route::middleware(['auth:user'])->prefix('generate')->group(function () {
        Route::post('/text', [GenerateController::class, 'generateText'])->name('generate.text');
        Route::post('/image', [GenerateController::class, 'generateImage'])->name('generate.image');
        Route::get('/image', [GenerateController::class, 'getGeneratedImage'])->name('generate.image.get');
    });

    Route::middleware(['auth:user'])->prefix('posts')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('posts.index');
        Route::get('/latest', [PostController::class, 'latest'])->name('posts.latest');
        Route::get('/schedule', [PostController::class, 'getSchedule'])->name('posts.schedule');
        Route::post('/schedule/change', [PostController::class, 'changeSchedule'])->name('posts.schedule');
        Route::post('/', [PostController::class, 'store'])->name('posts.store');
        Route::get('/{id}', [PostController::class, 'show'])->name('posts.show');
        Route::delete('/{id}', [PostController::class, 'destroy'])->name('posts.destroy');
        Route::post('/auto-create', [PostController::class, 'autoCreate'])->name('posts.create.auto');
    });

    Route::middleware(['auth:user'])->prefix('social')->group(function () {
        Route::get('list', [SocialAccountController::class, 'index'])->name('social.account.index');
        Route::get('small-list', [SocialAccountController::class, 'smallList'])->name('social.account.list.small');
        Route::delete('/{id}', [SocialAccountController::class, 'destroy'])->name('social.account.destroy');
        Route::put('/{id}', [SocialAccountController::class, 'update'])->name('social.account.update');
        Route::get('{provider}/redirect', [SocialAccountController::class, 'redirectToProvider'])->name('social.redirect');
        Route::get('{provider}/callback', [SocialAccountController::class, 'handleProviderCallback'])->name('social.callback');
    });

    Route::get('n8n/callback', [PostController::class, 'n8nCallback'])->name('n8n.callback');

    Route::get('posts/media/{id}', [PostController::class, 'getMedia'])->name('posts.media');

});
