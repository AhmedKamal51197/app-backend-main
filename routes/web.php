<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * Default route
 */
Route::get('/', function () {
    error_log("Hello World");
    error_log("Hello World");
    return view('welcome');
    //@todo: Add the redirect here
//    return redirect(config('app.frontend_url'));
});


Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified-email'])
    ->get('/dashboard', function () {
        return Inertia::render('Dashboard', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    })->name('dashboard');
