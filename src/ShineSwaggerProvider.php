<?php
namespace Shinedira\ShineSwagger;

use Illuminate\Support\ServiceProvider;
use Shinedira\ShineSwagger\Console\ProcessCommand;

class ShineSwaggerProvider extends ServiceProvider 
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            ProcessCommand::class
        ]);

        $this->mergeConfigFrom(
            __DIR__.'/config/shineSwagger.php', 'shine-swagger'
        );
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/views', 'shine-swagger');

        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
        }
    }

    protected function registerPublishing()
    {
        $this->publishes([
            __DIR__.'/config/shineSwagger.php' => config_path('shineSwagger.php'),
        ], 'shine-swagger');

        $this->publishes([
            __DIR__.'/public/shine-swagger' => public_path('shine-swagger'),
        ], 'shine-swagger');
    }
}