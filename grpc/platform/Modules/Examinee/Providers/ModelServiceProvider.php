<?php

namespace Modules\Examinee\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Examinee\Entities\ExamineeDeviceProbing;
use Modules\Examination\Entities\ExaminationExaminee;
use Modules\Examinee\Entities\FaceUser;
use Modules\Examinee\Entities\ExamineeOperation;
use App\Factories\Face\Face;

class ModelServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // ...
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        ExamineeDeviceProbing::saved(function ($model) {
            if (! empty($model->is_camera) &&
                ! empty($model->is_microphone) &&
                ! empty($model->is_chrome) &&
                ! empty($model->is_mc_ide) &&
                ! empty($model->is_scratch_ide) &&
                ! empty($model->is_python_ide) &&
                ! empty($model->is_c_ide)) {
                    $model->eexaminee->testing_status = ExaminationExaminee::TESTING_STATUS_OK;
            } else {
                if (empty($model->eexaminee->testing_status)) {
                    $model->eexaminee->testing_status = ExaminationExaminee::TESTING_STATUS_OFF;
                }
            }

            $model->eexaminee->save();
        });

        FaceUser::created(function ($model) {
            if (! empty($model->status)) {
                ExamineeOperation::create([
                    "examination_examinee_id" => $model->examination_examinee_id,
                    "source_id" => $model->id,
                    "category" => ExamineeOperation::CATEGORY_FACE,
                    'remark' => Face::$tips[$model->status] ?? ''
                ]);
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
