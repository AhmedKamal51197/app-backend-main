<?php

use App\Http\Controllers\Admin\Chat\AdminChatController;
use App\Http\Controllers\Admin\Message\AdminMessageController;
use Illuminate\Support\Facades\Route;

Route::prefix('chats')->group(function () {
    Route::get('/', [AdminChatController::class, 'index'])->middleware('permission:Index Chats');
    Route::post('/open', [AdminChatController::class, 'openChat'])->middleware('permission:Open Chats');
    Route::prefix('{chat}')->group(function () {
        Route::get('/', [AdminChatController::class, 'show'])->middleware('permission:Details Chats');
        Route::post('/messages', [AdminMessageController::class, 'store'])->middleware('permission:Send Chat Messages');
    });
});
