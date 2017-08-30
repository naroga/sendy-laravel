<?php

namespace BuddyAd\Sendy;

use Illuminate\Support\ServiceProvider;

/**
 * Class SendyServiceProvider
 *
 * @package BuddyAd\Sendy
 */
class SendyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/sendy.php' => config_path('sendy.php')
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function register()
    {
        $this->app->singleton(Sendy::class, function ($app) {
            return new Sendy($app['config']['sendy']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return ['sendy'];
    }
}
