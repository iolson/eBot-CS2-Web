<?php

use App\Http\Controllers\Admin\AdvertisingController;
use App\Http\Controllers\Admin\ConfigController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MatchController as AdminMatchController;
use App\Http\Controllers\Admin\SeasonController as AdminSeasonController;
use App\Http\Controllers\Admin\ServerController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\MatchController;
use App\Http\Controllers\Frontend\SeasonController;
use App\Http\Controllers\Frontend\StatsController;
use App\Http\Controllers\Frontend\StreamController;
use App\Http\Controllers\Frontend\WidgetController;
use App\Http\Middleware\RequireAdmin;
use Illuminate\Support\Facades\Route;

// -------------------------------------------------------------------------
// Public frontend routes
// -------------------------------------------------------------------------

Route::get('/', [HomeController::class, 'index'])->name('home');

// Matches (public viewer)
Route::get('/matchs', [MatchController::class, 'index'])->name('matchs.index');
Route::get('/matchs/archived', [MatchController::class, 'archived'])->name('matchs.archived');
Route::get('/matchs/{match}', [MatchController::class, 'show'])->name('matchs.show');
Route::post('/matchs/{match}/heatmap-data', [MatchController::class, 'heatmapData'])->name('matchs.heatmap-data');
Route::get('/matchs/{match}/logs', [MatchController::class, 'logs'])->name('matchs.logs');
Route::get('/matchs/{match}/export/players', [MatchController::class, 'exportPlayers'])->name('matchs.export.players');
Route::get('/matchs/{match}/export/rounds', [MatchController::class, 'exportRounds'])->name('matchs.export.rounds');
Route::get('/matchs/{match}/export/kills', [MatchController::class, 'exportKills'])->name('matchs.export.kills');
Route::get('/matchs/{match}/export/estats', [MatchController::class, 'exportEstats'])->name('matchs.export.estats');

// Statistics
Route::get('/stats', [StatsController::class, 'index'])->name('stats.index');
Route::get('/stats/global', [StatsController::class, 'global'])->name('stats.global');
Route::get('/stats/player/{steamid}', [StatsController::class, 'player'])->name('stats.player');
Route::get('/stats/maps', [StatsController::class, 'maps'])->name('stats.maps');
Route::get('/stats/weapons', [StatsController::class, 'weapons'])->name('stats.weapons');
Route::get('/stats/entry-kills', [StatsController::class, 'entryKills'])->name('stats.entry-kills');
Route::get('/stats/gunround', [StatsController::class, 'gunRound'])->name('stats.gunround');

// Seasons
Route::get('/seasons', [SeasonController::class, 'index'])->name('seasons.index');
Route::get('/seasons/{season}/select', [SeasonController::class, 'select'])->name('seasons.select');

// Stream (spectator view — uses stream layout)
Route::get('/stream/{match}', [StreamController::class, 'show'])->name('stream.show');

// Widgets (embeddable — use widget layout)
Route::get('/widget/match/{match}/players', [WidgetController::class, 'matchPlayers'])->name('widget.match-players');
Route::get('/widget/live-stats', [WidgetController::class, 'liveStats'])->name('widget.live-stats');

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
        Route::get('matchs/archived', [AdminMatchController::class, 'archived'])->name('matchs.archived');
        Route::post('matchs/archive-all', [AdminMatchController::class, 'archiveAll'])->name('matchs.archive-all');
        Route::post('matchs/start-all', [AdminMatchController::class, 'startAll'])->name('matchs.start-all');
        Route::post('matchs/{match}/start', [AdminMatchController::class, 'start'])->name('matchs.start');
        Route::post('matchs/{match}/stop', [AdminMatchController::class, 'stop'])->name('matchs.stop');
        Route::post('matchs/{match}/stop-back', [AdminMatchController::class, 'stopBack'])->name('matchs.stop-back');
        Route::post('matchs/{match}/pause-unpause', [AdminMatchController::class, 'pauseUnpause'])->name('matchs.pause-unpause');
        Route::post('matchs/{match}/force-start', [AdminMatchController::class, 'forceStart'])->name('matchs.force-start');
        Route::post('matchs/{match}/force-knife', [AdminMatchController::class, 'forceKnife'])->name('matchs.force-knife');
        Route::post('matchs/{match}/force-knife-end', [AdminMatchController::class, 'forceKnifeEnd'])->name('matchs.force-knife-end');
        Route::post('matchs/{match}/pass-knife', [AdminMatchController::class, 'passKnife'])->name('matchs.pass-knife');
        Route::post('matchs/{match}/reset', [AdminMatchController::class, 'reset'])->name('matchs.reset');
        Route::post('matchs/{match}/archive', [AdminMatchController::class, 'setArchive'])->name('matchs.archive');
        Route::post('matchs/{match}/duplicate', [AdminMatchController::class, 'duplicate'])->name('matchs.duplicate');
        Route::patch('matchs/{match}/score', [AdminMatchController::class, 'editScore'])->name('matchs.edit-score');
        Route::resource('matchs', AdminMatchController::class);

        // Seasons
        Route::post('seasons/{season}/deactivate', [AdminSeasonController::class, 'deactivate'])->name('seasons.deactivate');
        Route::resource('seasons', AdminSeasonController::class);

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
