<?php

use App\Http\Controllers\Api\Portfolio\PortfolioController;
use Illuminate\Support\Facades\Route;

// A freelancer's works and one work's details are public (guest mode).
Route::middleware('auth.optional')->prefix('portfolios')->group(function () {
    Route::get('/users/{user}', [PortfolioController::class, 'seekerIndex'])->whereUuid('user');
    Route::get('/{portfolio}', [PortfolioController::class, 'show'])->whereUuid('portfolio');
});

Route::prefix('portfolios')->middleware('auth:api')->group(callback: function () {
    Route::get('/', [PortfolioController::class, 'index']);
    Route::get('/info', [PortfolioController::class, 'info']);
    Route::get('/favorites', [PortfolioController::class, 'favorites']);
    Route::post('/add', [PortfolioController::class, 'store']);
    Route::prefix('{portfolio}')->group(function () {
        Route::post('/edit', [PortfolioController::class, 'edit']);
        Route::delete('delete', [PortfolioController::class, 'delete']);
        Route::post('/toggle-hidden', [PortfolioController::class, 'toggleHidden']);
        Route::post('/toggle-favorite', [PortfolioController::class, 'toggleFavorite']);
        Route::prefix('skills')->group(function () {
            Route::post('/add', [PortfolioController::class, 'addSkills']);
            Route::post('/delete', [PortfolioController::class, 'removeSkills']);
        });
        Route::prefix('attachments')->middleware('auth:api')->group(callback: function () {
            Route::post('/add', [PortfolioController::class, 'addAttachment']);
            Route::prefix('{attachment}')->group(function () {
                Route::delete('delete', [PortfolioController::class, 'deleteAttachment']);
            });
        });
    });
});
