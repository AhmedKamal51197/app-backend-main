<?php

use App\Http\Controllers\Admin\Analysis\AnalysisController;
use Illuminate\Support\Facades\Route;

Route::prefix('analysis')->group(callback: function () {
    Route::get('/', [AnalysisController::class, 'analysis']);
});