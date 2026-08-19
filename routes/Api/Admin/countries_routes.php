<?php

use App\Http\Controllers\Admin\Country\CountryController;
use Illuminate\Support\Facades\Route;

Route::prefix('countries')->group(callback: function () {
    Route::get('/', [CountryController::class, 'index']);
    Route::post('/add', [CountryController::class, 'store']);
    Route::prefix('{country}')->group(function () {
        Route::get('/', [CountryController::class, 'show']);
        Route::delete('/delete', [CountryController::class, 'delete']);
    });
});