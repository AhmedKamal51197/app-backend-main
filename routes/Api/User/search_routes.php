<?php

use App\Http\Controllers\Api\Search\SearchController;
use App\Http\Controllers\Api\Service\ServiceSearchController;
use Illuminate\Support\Facades\Route;

Route::prefix('search')->middleware('auth:api')->group(callback: function () {
    Route::post('/sub-category', ServiceSearchController::class);
    Route::post('/', [SearchController::class, 'search']);
});
