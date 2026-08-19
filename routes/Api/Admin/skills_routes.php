<?php

use App\Http\Controllers\Admin\Skill\SkillController;
use Illuminate\Support\Facades\Route;

Route::prefix('skills')->group(callback: function () {
    Route::prefix('{skill}')->group(function () {
        Route::get('/', [SkillController::class, 'show']);
        Route::post('/edit', [SkillController::class, 'edit']);
        Route::delete('/delete', [SkillController::class, 'delete']);
    });
});