<?php

use App\Http\Controllers\Api\Home\HomeController;
use Illuminate\Support\Facades\Route;

// Public home sections (guest mode); a token is optional.
Route::prefix('home')->middleware('auth.optional')->group(callback: function () {
    Route::get('/users', [HomeController::class, 'users']);
    Route::get('/one-time-services', [HomeController::class, 'oneTimeServices']);
    Route::get('/part-time-services', [HomeController::class, 'partTimeServices']);
});
