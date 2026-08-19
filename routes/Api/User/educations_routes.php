<?php

use App\Http\Controllers\Api\Education\EducationController;
use Illuminate\Support\Facades\Route;

Route::prefix('educations')->middleware('auth:api')->group(callback: function () {
    Route::get('/', [EducationController::class, 'index']);
    Route::get('/info', [EducationController::class, 'info']);
    Route::post('/add', [EducationController::class, 'store']);
    Route::prefix('{education}')->group(function () {
        Route::get('/', [EducationController::class, 'show']);
        Route::post('/edit', [EducationController::class, 'edit']);
        Route::delete('delete', [EducationController::class, 'delete']);
    });
});
