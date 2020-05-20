<?php

namespace Siam\Sms\Laravel;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
	/**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
        	__DIR__ . '/../../config/siam-sms.php',
            'siam-sms'
        );
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
        	__DIR__ . '/../../config/siam-sms.php' => config_path('siam-sms.php')
        ], 'config');
    }
}
