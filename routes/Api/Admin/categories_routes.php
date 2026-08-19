<?php

use App\Http\Controllers\Admin\Category\CategoryController;
use App\Http\Controllers\Admin\Skill\SkillController;
use App\Http\Controllers\Admin\SubCategory\SubCategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('categories')->group(callback: function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/add', [CategoryController::class, 'store']);
    Route::prefix('{category}')->group(function () {
        Route::get('/', [CategoryController::class, 'show']);
        Route::post('/edit', [CategoryController::class, 'edit']);
        Route::delete('/delete', [CategoryController::class, 'delete']);
        Route::prefix('sub-categories')->group(callback: function () {
            Route::get('/', [SubCategoryController::class, 'index']);
            Route::post('/add', [SubCategoryController::class, 'store']);
        });
        Route::prefix('skills')->group(callback: function () {
            Route::get('/', [SkillController::class, 'index']);
            Route::post('/add', [SkillController::class, 'store']);
        });
    });
});
