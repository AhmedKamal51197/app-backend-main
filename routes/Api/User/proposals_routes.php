<?php

use App\Http\Controllers\Api\Project\ProjectController;
use App\Http\Controllers\Api\Proposal\ProposalController;
use Illuminate\Support\Facades\Route;

Route::prefix('proposals')->middleware('auth:api')->group(function () {
    Route::get('/', [ProposalController::class, 'index']);
    Route::post('/add', [ProposalController::class, 'store']);
    Route::prefix('{proposal}')->group(function () {
        Route::get('/', [ProposalController::class, 'show']);
    });
});
