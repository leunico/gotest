<?php

namespace Modules\Personal\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Operate\Entities\Order;
use Modules\Personal\Entities\UserOrder;
use Modules\Educational\Entities\BiuniqueAppointment;

class ModelServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        UserOrder::created(function ($model) {
            if ($model->category == UserOrder::CATEGORY_STAR) {
                if ($model->target_type == BiuniqueAppointment::class) {
                    $user = $model->user;
                    if ($model->isExpenditure()) {
                        $user->star_amount = $user->star_amount - $model->amount;
                    } elseif ($model->isIncome()) {
                        $user->star_amount = $user->star_amount + $model->amount;
                    }
                    $user->save();
                }
                if ($model->target_type == Order::class) {
                    $user = $model->user;
                    $user->star_amount = $user->star_amount + $model->amount;
                    $user->save();
                }
            }

        });
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
