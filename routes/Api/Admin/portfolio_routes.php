<?php

use App\Http\Controllers\Admin\Portfolio\PortfolioController;
use Illuminate\Support\Facades\Route;

Route::prefix('portfolios')->group(function () {
    Route::get('/', [PortfolioController::class, 'index'])->middleware('permission:Index Portfolios');
    Route::prefix('{portfolio}')->group(function () {
        Route::get('/', [PortfolioController::class, 'show'])->middleware('permission:Details Portfolios');
        Route::post('/toggle-hidden', [PortfolioController::class, 'toggleHidden'])->middleware('permission:Toggle Portfolio Visibility');
        Route::delete('/delete', [PortfolioController::class, 'delete'])->middleware('permission:Delete Portfolios');
    });
});
