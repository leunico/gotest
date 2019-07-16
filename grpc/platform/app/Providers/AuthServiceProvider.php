<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Guards\JwtEventGuard;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // jwt-event
        Auth::extend('jwt_event', function ($app, $name, array $config) {
            return new JwtEventGuard($app['tymon.jwt'], Auth::createUserProvider($config['provider']), $app->request, $app->events);
        });
    }
}
