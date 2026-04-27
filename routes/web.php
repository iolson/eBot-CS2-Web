<?php

use App\Http\Controllers\Admin\AdvertisingController;
use App\Http\Controllers\Admin\ConfigController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MatchController;
use App\Http\Controllers\Admin\SeasonController;
use App\Http\Controllers\Admin\ServerController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\UserController;
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
// Admin routes
// -------------------------------------------------------------------------

Route::prefix('admin')
    ->middleware(['auth', RequireAdmin::class])
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Matches
        Route::get('matchs/archived', [MatchController::class, 'archived'])->name('matchs.archived');
        Route::post('matchs/archive-all', [MatchController::class, 'archiveAll'])->name('matchs.archive-all');
        Route::post('matchs/start-all', [MatchController::class, 'startAll'])->name('matchs.start-all');
        Route::post('matchs/{match}/start', [MatchController::class, 'start'])->name('matchs.start');
        Route::post('matchs/{match}/stop', [MatchController::class, 'stop'])->name('matchs.stop');
        Route::post('matchs/{match}/stop-back', [MatchController::class, 'stopBack'])->name('matchs.stop-back');
        Route::post('matchs/{match}/pause-unpause', [MatchController::class, 'pauseUnpause'])->name('matchs.pause-unpause');
        Route::post('matchs/{match}/force-start', [MatchController::class, 'forceStart'])->name('matchs.force-start');
        Route::post('matchs/{match}/force-knife', [MatchController::class, 'forceKnife'])->name('matchs.force-knife');
        Route::post('matchs/{match}/force-knife-end', [MatchController::class, 'forceKnifeEnd'])->name('matchs.force-knife-end');
        Route::post('matchs/{match}/pass-knife', [MatchController::class, 'passKnife'])->name('matchs.pass-knife');
        Route::post('matchs/{match}/reset', [MatchController::class, 'reset'])->name('matchs.reset');
        Route::post('matchs/{match}/archive', [MatchController::class, 'setArchive'])->name('matchs.archive');
        Route::post('matchs/{match}/duplicate', [MatchController::class, 'duplicate'])->name('matchs.duplicate');
        Route::patch('matchs/{match}/score', [MatchController::class, 'editScore'])->name('matchs.edit-score');
        Route::resource('matchs', MatchController::class);

        // Seasons
        Route::post('seasons/{season}/deactivate', [SeasonController::class, 'deactivate'])->name('seasons.deactivate');
        Route::resource('seasons', SeasonController::class);

        // Servers
        Route::resource('servers', ServerController::class)->except(['show', 'edit', 'update']);

        // Teams
        Route::get('teams/season-members', [TeamController::class, 'teamsInSeason'])->name('teams.season-members');
        Route::resource('teams', TeamController::class);

        // Configs
        Route::resource('configs', ConfigController::class)->except(['show']);

        // Advertising
        Route::post('advertising/{advertising}/deactivate', [AdvertisingController::class, 'deactivate'])->name('advertising.deactivate');
        Route::resource('advertising', AdvertisingController::class)->except(['show']);

        // Users
        Route::resource('users', UserController::class)->except(['show']);
    });
