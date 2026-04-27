<?php

namespace App\Providers;

use App\Auth\SfGuardUserProvider;
use App\Services\AesCtrService;
use App\Services\EbotCommandService;
use App\Services\JwtService;
use App\Services\StartGgService;
use App\Services\ToornamentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(JwtService::class, fn () => new JwtService(
            config('ebot.websocket_secret_key', '')
        ));

        $this->app->singleton(AesCtrService::class, fn () => new AesCtrService);

        $this->app->singleton(ToornamentService::class, fn () => new ToornamentService(
            config('ebot.toornament.id', ''),
            config('ebot.toornament.secret', ''),
            config('ebot.toornament.api_key', ''),
        ));

        $this->app->singleton(StartGgService::class, fn () => new StartGgService(
            config('ebot.startgg.token', ''),
        ));

        $this->app->singleton(EbotCommandService::class, fn ($app) => new EbotCommandService(
            $app->make(AesCtrService::class),
            config('ebot.websocket_url', 'http://localhost:12360'),
        ));
    }

    public function boot(): void
    {
        Auth::provider('sfguard', function ($app, array $config) {
            return new SfGuardUserProvider($app['hash'], $config['model']);
        });

        // Share a JWT token with all views so layouts can pass it to window.ebotConfig.
        View::composer('*', function ($view) {
            try {
                /** @var JwtService $jwt */
                $jwt = app(JwtService::class);
                $user = auth()->user();
                $token = $user
                    ? $jwt->forAdmin($user->username ?? $user->getDisplayName())
                    : $jwt->forPublic();
            } catch (\Throwable) {
                $token = '';
            }

            $view->with('jwtToken', $token);
        });
    }
}
