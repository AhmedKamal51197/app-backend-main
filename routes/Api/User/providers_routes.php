<?php

use App\Http\Controllers\Api\Provider\ProviderController;
use Illuminate\Support\Facades\Route;

// Public freelancer profile (guest mode); a token is optional.
Route::prefix('providers')->middleware('auth.optional')->group(function () {
    Route::prefix('{user}')->group(function () {
        Route::get('/', [ProviderController::class, 'profile']);
    });
});
