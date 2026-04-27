<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Middleware\RequireAdmin;
use Illuminate\Support\Facades\Route;

// -------------------------------------------------------------------------
// Public routes
// -------------------------------------------------------------------------

Route::get('/', function () {
    return view('welcome');
})->name('home');

// -------------------------------------------------------------------------
// Auth routes
// -------------------------------------------------------------------------

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// -------------------------------------------------------------------------
// Admin routes (Phase 5 will populate these)
// -------------------------------------------------------------------------

Route::prefix('admin')
    ->middleware(['auth', RequireAdmin::class])
    ->name('admin.')
    ->group(function () {
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');
    });
