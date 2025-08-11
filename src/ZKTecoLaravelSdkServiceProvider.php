<?php

namespace Technophilic\ZKTecoLaravelSdk;

use Illuminate\Support\ServiceProvider;

class ZKTecoLaravelSdkServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/zkteco-laravel-sdk.php' => config_path('zkteco-laravel-sdk.php'),
        ], 'config');

        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        // If you want to publish routes (optional)
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/routes/web.php' => base_path('routes/zkt-laravel-sdk.php'),
            ], 'routes');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/zkteco-laravel-sdk.php', 'zkteco-laravel-sdk');

        $this->app->singleton('zkteco-laravel-sdk', function () {
            return new ZKTecoLaravelSdkService(
                config('zkteco-laravel-sdk.ip', '192.168.1.163'),
                config('zkteco-laravel-sdk.port', 4370)
            );
        });
    }
}
