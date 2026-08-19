<?php

use App\Http\Controllers\Api\Category\CategoryController;
use App\Http\Controllers\Api\Skill\SkillController;
use App\Http\Controllers\Api\SubCategory\SubCategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::prefix('{category}')->group(function () {
        Route::get('/', [CategoryController::class, 'show']);

        Route::prefix('sub-categories')->group(callback: function () {
            Route::get('/', [SubCategoryController::class, 'index']);
        });

        Route::prefix('skills')->group(callback: function () {
            Route::get('/', [SkillController::class, 'index']);
        });
    });
});
