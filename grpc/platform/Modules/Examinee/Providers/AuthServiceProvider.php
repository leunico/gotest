<?php

namespace Modules\Examinee\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Modules\Examinee\Entities\ExamineeAnswer;

class AuthServiceProvider extends ServiceProvider
{

    /**
     * 策略映射。[The policy mappings for the application.]
     *
     * @var array
     */
    protected $policies = [
        ExamineeAnswer::class => \Modules\Examinee\Policies\ExamineeAnswerPolicy::class,
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
