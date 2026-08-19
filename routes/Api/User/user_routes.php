<?php

use App\Http\Controllers\Api\Dashboard\DashboardController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\UserCategory\UserCategoryController;
use App\Http\Controllers\Api\UserCertificate\UserCertificateController;
use App\Http\Controllers\Api\UserSkill\UserSkillController;
use App\Http\Controllers\Api\UserStatus\UserStatusController;
use App\Http\Controllers\Api\UserSubCategory\UserSubCategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->middleware('auth:api')->group(function () {
    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/edit/avatar', [UserController::class, 'changeProfilePicture']);
    Route::post('/change/password', [UserController::class, 'editPassword']);
    Route::post('/edit/profile', [UserController::class, 'editProfile']);
    Route::post('/edit/category', [UserController::class, 'editCategory']);
    Route::post('/send-notification', [UserController::class, 'sendNotification']);
    Route::post('/store/category', [UserCategoryController::class, 'storeUserCategory']);
    Route::post('/store/sub-categories', [UserSubCategoryController::class, 'storeUserSubCategories']);
    Route::post('/store/skills', [UserSkillController::class, 'storeUserSkills']);
    Route::post('/delete/skills', [UserSkillController::class, 'removeUserSkills']);
    Route::post('/delete/sub-categories', [UserSubCategoryController::class, 'removeUserSubCategories']);
    Route::post('/store/certificate', [UserCertificateController::class, 'storeUserCertificate']);
    Route::post('delete/certificate/{certificate}', [UserCertificateController::class, 'removeUserCertificate']);
    Route::post('edit/certificate/{certificate}', [UserCertificateController::class, 'editUserCertificate']);
    Route::post('/wallet/add', [UserController::class, 'storeWallet']);
    Route::get('home', [UserController::class, 'home']);
    Route::get('notifications', [UserController::class, 'notifications']);
    Route::get('dashboard', [DashboardController::class, 'analytics']);
    Route::post('change-status', [UserStatusController::class, 'changeStatus']);
});
