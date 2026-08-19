<?php

use App\Http\Controllers\Admin\Color\ColorController;
use Illuminate\Support\Facades\Route;

Route::prefix('colors')->group(function () {
    Route::get('/', [ColorController::class, 'index']);
    Route::post('/add', [ColorController::class, 'store']);
    Route::prefix('/{color}')->group(function () {
        Route::get('/', [ColorController::class, 'show']);
        Route::post('/edit', [ColorController::class, 'update']);
        Route::delete('/delete', [ColorController::class, 'destroy']);
    });
});