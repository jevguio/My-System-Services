<?php

namespace plugins\helloworld;

use Illuminate\Support\ServiceProvider;
use App\PluginHook;

class PluginServiceProvider extends ServiceProvider
{
    
    public function boot()
    {
        
        $jsonPath = __DIR__ . '/plugin.json';

        if (file_exists($jsonPath)) {
            $data = json_decode(file_get_contents($jsonPath), true);

            PluginHook::registerPlugin(
                $data['title'] ?? 'Unknown',
                $data['name'] ?? 'Unknown',
                $data['version'] ?? '0.0.0',
                $data['description'] ?? '',
                $data['gitUrl'] ?? ''
            );
        }
        PluginHook::addTopMenu('Test Plugin', 'test.plugin');
        
        $this->loadViewsFrom(__DIR__.'/resources/view', 'helloworld');

        // Register routes
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
    }
    
}
