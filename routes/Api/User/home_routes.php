<?php

use App\Http\Controllers\Api\Home\HomeController;
use Illuminate\Support\Facades\Route;

Route::prefix('home')->middleware('auth:api')->group(callback: function () {
    Route::get('/users', [HomeController::class, 'users']);
    Route::get('/one-time-services', [HomeController::class, 'oneTimeServices']);
    Route::get('/part-time-services', [HomeController::class, 'partTimeServices']);
});
