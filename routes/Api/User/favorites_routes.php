<?php

use App\Http\Controllers\Api\Favorite\FavoriteServiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('favorites')->middleware('auth:api')->group(function () {
    Route::get('/', [FavoriteServiceController::class, 'index']);
    Route::prefix('{service}')->group(function () {
        Route::post('/add', [FavoriteServiceController::class, 'store']);
        Route::delete('/delete', [FavoriteServiceController::class, 'delete']);
    });
});
