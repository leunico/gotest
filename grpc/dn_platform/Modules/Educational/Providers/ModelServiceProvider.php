<?php

namespace Modules\Educational\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Educational\Entities\StudyClass;
use Modules\Personal\Entities\CourseUser;
use Modules\Educational\Entities\ClassStudent;
use Modules\Educational\Entities\TeacherOfficeTime;

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
        StudyClass::updated(function ($model) {
            if ($model->unlocak_times != (array) json_decode($model->getOriginal('unlocak_times'), true)) {
                $model->courseLessons()->sync([]);
            }
        });

        ClassStudent::deleting(function ($model) {
            CourseUser::where('user_id', $model->user_id)
                ->whereIn('course_id', $model->courses->pluck('course_id'))
                ->update(['class_id' => 0]);
        });

        TeacherOfficeTime::updated(function ($model) {
            if ($model->status != (array) json_decode($model->getOriginal('status'), true)) {
                $model->where('appointment_date', $model->appointment_date)
                    ->where('user_id', $model->user_id)
                    ->update(['status' => $model->status]);
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
