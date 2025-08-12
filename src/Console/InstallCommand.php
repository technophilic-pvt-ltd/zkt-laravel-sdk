<?php

namespace Technophilic\ZKTecoLaravelSDK\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'zkteco:install';
    protected $description = 'Install ZKTeco Laravel SDK';

    public function handle()
    {
        $this->info('Installing ZKTeco Laravel SDK...');

        // Publish all files
        $this->call('vendor:publish', [
            '--tag' => 'zkteco-all',
            '--force' => true,
        ]);

        $this->info('ZKTeco Laravel SDK installed successfully!');
        $this->info('Published files:');
        $this->line('- Controllers: app/Http/Controllers/ZKTeco/');
        $this->line('- Routes: routes/zkteco/');
        $this->line('- Config: config/zkteco.php');
        $this->line('- Views: resources/views/zkteco/');
        $this->addRoutesToWebPhp();


        $this->newLine();
        $this->info('Next steps:');
        $this->line('1. Add "require base_path(\'routes/zkteco.php\');" to routes/zkteco.php');
        $this->line('2. Configure your device settings in config/zkteco.php');
        $this->line('3. Run: php artisan migrate (if you have migrations)');
    }

    protected function addRoutesToWebPhp()
    {
        $webRoutesPath = base_path('routes/web.php');

        $routeIncludes = [
            "require base_path('routes/ZKTeco/iclock.php');",
            "require base_path('routes/ZKTeco/zkteco.php');"
        ];

        if (!file_exists($webRoutesPath)) {
            $this->warn('routes/web.php not found. Please manually add the route includes.');
            return;
        }


        $contents = file_get_contents($webRoutesPath);
        $modified = false;

        foreach ($routeIncludes as $routeInclude) {
            if (!str_contains($contents, $routeInclude)) {
                $contents .= PHP_EOL . $routeInclude;
                $modified = true;
            }
        }

        if ($modified) {
            file_put_contents($webRoutesPath, $contents);
            $this->info('Added ZKTeco route includes to web.php');
        } else {
            $this->info('ZKTeco routes already included in web.php');
        }
    }
}
