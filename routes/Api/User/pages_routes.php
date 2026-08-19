<?php

use App\Http\Controllers\Api\Page\PageController;
use Illuminate\Support\Facades\Route;

Route::prefix('pages')->group(function () {
    Route::get('/{page:key}', [PageController::class, 'show']);
});
