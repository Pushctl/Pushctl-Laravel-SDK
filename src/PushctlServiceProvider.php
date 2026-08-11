<?php

declare(strict_types=1);

namespace Flowgistics\Pushctl;

use Illuminate\Support\ServiceProvider;

class PushctlServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/pushctl-laravel-sdk.php', 'pushctl-laravel-sdk');

        $this->app->singleton(Pushctl::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/pushctl-laravel-sdk.php' => config_path('pushctl-laravel-sdk.php'),
        ], ['pushctl-laravel-sdk', 'pushctl-laravel-sdk-config']);
    }
}
