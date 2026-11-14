<?php

use App\Http\Controllers\Api\Setting\PublicSettingController;
use Illuminate\Support\Facades\Route;

Route::get('settings/public', [PublicSettingController::class, 'show'])->middleware('public.content');
