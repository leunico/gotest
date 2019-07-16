<?php

namespace Modules\ExaminationPaper\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\ExaminationPaper\Entities\Question;
use Modules\ExaminationPaper\Entities\MarkingRecord;
use Modules\ExaminationPaper\Observers\MarkingRecordObserver;

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

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Question::deleting(function ($model) {
            $model->options->map(function ($item) { // todo 这里应该有更优化的写法.
                $item->delete();
            });

            $majorProblem = $model->majorProblem;
            $majorProblem->total_score = $majorProblem->total_score - $model->score;
            $majorProblem->save();
        });

        Question::saved(function ($model) {
            $majorProblem = $model->majorProblem;
            if ($model && $model->getOriginal('score')){
                $majorProblem->total_score = $model->score > $model->getOriginal('score') ?
                    $majorProblem->total_score + ($model->score - $model->getOriginal('score')) :
                    $majorProblem->total_score - ($model->getOriginal('score') - $model->score);
            } else {
                $majorProblem->total_score = $majorProblem->total_score + $model->score;
            }
            $majorProblem->save();
        });

        MarkingRecord::observe(MarkingRecordObserver::class);
    }
}
