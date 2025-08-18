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
            // $csrfMiddleware = app(VerifyCsrfToken::class);

            // // Merge your routes with the existing except array
            // $csrfMiddleware->except([
            //     'iclock/*',
            // ]);

            $this->commands([
                \Technophilic\ZKTecoLaravelSDK\Console\InstallCommand::class,
            ]);
        });
        // // Publish config
        // $this->publishes([
        //     __DIR__ . '/../config/zkteco.php' => config_path('zkteco.php'),
        // ], 'zkteco-config');

        // // Publish controllers to user's app
        // $this->publishes([
        //     __DIR__ . '/../stubs/Controllers' => app_path('Http/Controllers/ZKTeco'),
        // ], 'zkteco-controllers');

        // // Publish routes to user's app
        // $this->publishes([
        //     __DIR__ . '/../stubs/routes' => base_path('routes/ZKTeco'),
        // ], 'zkteco-routes');

        // ADD THIS: Publish all together
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
