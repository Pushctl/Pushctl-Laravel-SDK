<?php

declare(strict_types=1);

namespace Flowgistics\Pushctl;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\ServiceProvider;

class PushctlServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/pushctl-laravel-sdk.php', 'pushctl-laravel-sdk');

        $this->app->singleton(Pushctl::class, function (): Pushctl {
            $config = $this->app->make(Repository::class);

            return new Pushctl(
                http: $this->app->make(Factory::class),
                url: (string) $config->get('pushctl-laravel-sdk.url', ''),
                token: (string) $config->get('pushctl-laravel-sdk.token', ''),
                timeout: (int) $config->get('pushctl-laravel-sdk.timeout', 10),
            );
        });
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
