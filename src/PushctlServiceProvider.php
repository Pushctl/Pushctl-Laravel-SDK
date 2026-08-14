<?php

declare(strict_types=1);

namespace Pushctl\PushctlLaravel;

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
        $this->mergeConfigFrom(__DIR__.'/../config/pushctl.php', 'pushctl');

        $this->app->singleton(Pushctl::class, function (): Pushctl {
            $config = $this->app->make(Repository::class);

            return new Pushctl(
                http: $this->app->make(Factory::class),
                url: (string) $config->get('pushctl.url', ''),
                token: (string) $config->get('pushctl.token', ''),
                timeout: (int) $config->get('pushctl.timeout', 10),
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
            __DIR__.'/../config/pushctl.php' => config_path('pushctl.php'),
        ], ['pushctl', 'pushctl-config']);
    }
}
