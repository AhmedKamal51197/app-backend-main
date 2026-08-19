<?php

use App\Http\Controllers\Api\Skill\SkillController;
use Illuminate\Support\Facades\Route;

Route::prefix('skills')->group(function () {
    Route::get('/{skill}', [SkillController::class, 'show']);
});
