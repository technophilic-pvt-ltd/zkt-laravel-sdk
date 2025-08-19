<?php

namespace Technophilic\ZKTecoLaravelSDK;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\ServiceProvider;

class ZKTecoServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bind ZKTeco as singleton
        $this->app->singleton(ZKTeco::class, function ($app) {
            return new ZKTeco();
        });

        // Merge config
        $this->mergeConfigFrom(__DIR__ . '/../config/zkteco.php', 'zkteco');
    }

    public function boot()
    {
        $this->app->booted(function () {
            $this->commands([
                \Technophilic\ZKTecoLaravelSDK\Console\InstallCommand::class,
            ]);
        });

        $this->publishes([
            __DIR__ . '/../config/zkteco.php' => config_path('zkteco.php'),
            __DIR__ . '/../stubs/Controllers' => app_path('Http/Controllers/ZKTeco'),
            __DIR__ . '/../stubs/routes' => base_path('routes/ZKTeco'),
        ], 'zkteco-all');

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Technophilic\ZKTecoLaravelSDK\Console\InstallCommand::class,
            ]);
        }
    }
}
