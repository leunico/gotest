<?php

namespace Modules\Course\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Modules\Course\Entities\CourseLesson;
use Modules\Course\Policies\CourseLessonPolicy;
use Modules\Course\Entities\Course;
use Modules\Course\Policies\CoursePolicy;
use Modules\Course\Entities\MusicTheory;
use Modules\Course\Entities\CourseSection;
use Modules\Course\Policies\CourseSectionPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * 策略映射。[The policy mappings for the application.]
     *
     * @var array
     */
    protected $policies = [
        CourseSection::class => CourseSectionPolicy::class,
        CourseLesson::class => CourseLessonPolicy::class,
        Course::class => CoursePolicy::class,
        MusicTheory::class => CoursePolicy::class,
    ];

    /**
    * Register any authentication / authorization services.
    *
    * @return void
    */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
