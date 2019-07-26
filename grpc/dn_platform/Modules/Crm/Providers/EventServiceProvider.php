<?php

namespace Modules\Crm\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Crm\Listeners\SyncOrder;
use Modules\Crm\Listeners\SyncUser;
use Modules\Operate\Events\OrderChange;
use Modules\Personal\Events\ChangeUser;

/**
 * Class EventServiceProvider
 * @package Modules\Crm\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        OrderChange::class => [
            SyncOrder::class,
        ],
        ChangeUser::class   => [
            SyncUser::class,
        ],
    ];

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the application's event listeners.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
