<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GrpcClient\GrpcManager;

class GrpcServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('grpc', function ($app) {
            $config = $app->make('config')->get('database.grpc', []);

            return new GrpcManager($app, $config);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // ...
    }
}
