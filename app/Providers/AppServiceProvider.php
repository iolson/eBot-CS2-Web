<?php

namespace App\Providers;

use App\Auth\SfGuardUserProvider;
use App\Services\AesCtrService;
use App\Services\JwtService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(JwtService::class, fn () => new JwtService(
            config('ebot.websocket_secret_key', '')
        ));

        $this->app->singleton(AesCtrService::class, fn () => new AesCtrService());
    }

    public function boot(): void
    {
        Auth::provider('sfguard', function ($app, array $config) {
            return new SfGuardUserProvider($app['hash'], $config['model']);
        });
    }
}
