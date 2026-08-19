<?php

use App\Http\Controllers\Api\SubCategory\SubCategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('sub-categories')->group(function () {
    Route::prefix('{subCategory}')->group(function () {
        Route::get('/', [SubCategoryController::class, 'show']);
    });
});
