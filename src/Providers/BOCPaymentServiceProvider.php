<?php

namespace Byross\BOCPayment\Providers;

use Byross\BOCPayment\BOCPayment;
use Illuminate\Support\ServiceProvider;

class BOCPaymentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'lighthouse-media-library');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'lighthouse-media-library');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

//        php artisan vendor:publish --provider="Byross\BOCPayment\Providers\BOCPaymentServiceProvider" --tag="config"

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/config.php' => config_path('boc-macau-payment.php'),
            ], 'config');

//            $this->publishes([
//                __DIR__.'/../../database/migrations/create_media_table.stub' =>
//                    database_path('migrations/' . date('Y_m_d_His', time()) . '_create_lighthouse_media_table.php'),
//                // you can add any number of migrations here
//            ], 'migrations');


            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/lighthouse-media-library'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/lighthouse-media-library'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/lighthouse-media-library'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'boc-macau-payment');

        // Register the main class to use with the facade
        $this->app->singleton('boc-macau-payment', function () {
            return new BOCPayment();
        });

//        $this->app->bind('lighthouse-media-library', function($app) {
//            return new GraphqlMediaLibrary();
//        });
    }
}
