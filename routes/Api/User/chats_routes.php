<?php

use App\Http\Controllers\Api\Chat\ChatController;
use App\Http\Controllers\Api\Message\MessageController;
use App\Http\Controllers\Api\Offer\OfferController;
use Illuminate\Support\Facades\Route;

Route::prefix('chats')->middleware('auth:api')->group(callback: function () {
    Route::get('/', [ChatController::class, 'index']);
    Route::post('/open', [ChatController::class, 'openChat']);
    Route::prefix('{chat}')->group(function () {
        Route::get('/', [ChatController::class, 'show']);
        Route::post('/messages', [MessageController::class, 'store']);
        Route::post('/offer', [OfferController::class, 'store']);
    });
});
