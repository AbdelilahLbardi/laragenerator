<?php

namespace AbdelilahLbardi\LaraGenerator\Providers;

use Illuminate\Support\ServiceProvider;

class LaraGeneratorServiceProvider extends ServiceProvider
{

	/**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__.'/../Templates/' => base_path('Templates')], 'templates');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerLaracastsGenerator();
        $this->registerLaraGenerator();
    }


    /**
    * Register Laracasts Generator service provider
    */
    public function registerLaracastsGenerator()
    {
        $this->app->register('Laracasts\Generators\GeneratorsServiceProvider');
    }

    /**
    * Register LaraGenerator service provider
    */
    public function registerLaraGenerator()
    {
        $this->app->singleton('command.abdelilahlbardi.laragenerator', function ($app) {
            return $app['AbdelilahLbardi\LaraGenerator\Console\Commands\Generator'];
        });

        $this->commands('command.abdelilahlbardi.laragenerator');
    }    
}