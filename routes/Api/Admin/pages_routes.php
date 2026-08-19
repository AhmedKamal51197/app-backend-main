<?php

use App\Http\Controllers\Admin\PageController;
use Illuminate\Support\Facades\Route;

Route::prefix('pages')->group(function () {
    Route::get('/', [PageController::class, 'index'])->middleware('permission:Index Pages');
    Route::prefix('{page:key}')->group(function () {
        Route::get('/', [PageController::class, 'show'])->middleware('permission:Details Pages');
        Route::post('/edit', [PageController::class, 'update'])->middleware('permission:Edit Pages');
    });
});