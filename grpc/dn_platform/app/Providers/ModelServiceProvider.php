<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Role;
use App\UserAddress;
use App\User;

class ModelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Role::deleting(function ($model) {
            $model->syncPermissions([]);
        });

        UserAddress::saved(function ($model) {
            if ($model->province_id && $model->city_id && $model->district_id && $model->detail && $model->receiver) {
                $model->user()->update(['is_address' => User::IS_ADDRESS_ON]);
            } else {
                $model->user()->update(['is_address' => User::IS_ADDRESS_OFF]);
            }
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
