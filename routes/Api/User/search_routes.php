<?php

use App\Http\Controllers\Api\Search\SearchController;
use App\Http\Controllers\Api\Service\ServiceSearchController;
use Illuminate\Support\Facades\Route;

// Public search (guest mode); a token is optional.
Route::prefix('search')->middleware(['auth.optional', 'throttle:60,1'])->group(callback: function () {
    Route::post('/sub-category', ServiceSearchController::class);
    Route::post('/', [SearchController::class, 'search']);
});
