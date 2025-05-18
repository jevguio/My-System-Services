<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $pluginDirs = glob(base_path('plugins/*'), GLOB_ONLYDIR);

        foreach ($pluginDirs as $pluginPath) {
            $jsonPath = $pluginPath . '/plugin.json';

            if (file_exists($jsonPath)) {
                $meta = json_decode(file_get_contents($jsonPath), true);

                if (!empty($meta['enabled']) && !empty($meta['provider'])) {
                    if (class_exists($meta['provider'])) {
                        $this->app->register($meta['provider']);
                    }
                }
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
