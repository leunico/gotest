<?php

namespace Modules\Educational\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Modules\Educational\Entities\AuditionClass;
use Modules\Educational\Policies\AuditionClassPolicy;
use Modules\Educational\Entities\BiuniqueAppointment;
use Modules\Educational\Policies\BiuniqueAppointmentPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        AuditionClass::class => AuditionClassPolicy::class,
        BiuniqueAppointment::class => BiuniqueAppointmentPolicy::class
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerPolicies();
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
