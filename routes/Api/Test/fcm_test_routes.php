<?php

use App\Http\Controllers\Api\Test\FCMTestController;
use Illuminate\Support\Facades\Route;

Route::prefix('test')->group(function () {
    Route::post('send-notification', [FCMTestController::class, 'sendTestNotification']);
});
