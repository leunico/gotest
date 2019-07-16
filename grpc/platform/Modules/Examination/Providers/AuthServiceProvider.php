<?php

namespace Modules\Examination\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Modules\Examination\Entities\Examination;
use Modules\Examination\Entities\ExaminationExaminee;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * 策略映射。[The policy mappings for the application.]
     *
     * @var array
     */
    protected $policies = [
        Examination::class => \Modules\Examination\Policies\ExaminationPolicy::class,
        ExaminationExaminee::class => \Modules\Examination\Policies\ExaminationExamineePolicy::class,
    ];

    /**
    * Register any authentication / authorization services.
    *
    * @return void
    */
    public function boot()
    {
        // ...

        $this->registerPolicies();
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
