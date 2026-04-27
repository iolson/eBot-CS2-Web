<?php

namespace App\Providers;

use App\Auth\SfGuardUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Auth::provider('sfguard', function ($app, array $config) {
            return new SfGuardUserProvider($app['hash'], $config['model']);
        });
    }
}
