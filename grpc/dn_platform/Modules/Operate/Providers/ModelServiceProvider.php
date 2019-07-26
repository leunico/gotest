<?php

namespace Modules\Operate\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Operate\Entities\Order;
use Modules\Operate\Entities\WechatPushJob;
use Modules\Operate\Observers\WechatPushJobObserver;

class ModelServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot()
    {
        Order::deleting(function ($order) {
            $order->goods()->delete();
        });

        WechatPushJob::observe(WechatPushJobObserver::class);
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
