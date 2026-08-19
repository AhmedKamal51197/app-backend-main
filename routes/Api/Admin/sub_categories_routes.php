<?php

use App\Http\Controllers\Admin\SubCategory\SubCategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('sub-categories')->group(callback: function () {
    Route::prefix('{subCategory}')->group(function () {
        Route::get('/', [SubCategoryController::class, 'show']);
        Route::post('/edit', [SubCategoryController::class, 'edit']);
        Route::delete('/delete', [SubCategoryController::class, 'delete']);
    });
});