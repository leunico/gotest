<?php

namespace Modules\Course\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Course\Entities\Problem;
use Modules\Course\Entities\Tag;
use Modules\Course\Entities\MusicPractice;
use Illuminate\Database\Eloquent\Relations\Relation;

class ModelServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'music_practices' => MusicPractice::class,
        ]);

        Problem::deleting(function ($model) {
            $model->detail()->delete();
            $model->options->map(function ($item) { // todo 这里应该有更优化的写法，有老哥知道了mail我（867426952@qq.com）
                $item->delete();
            });
        });

        Tag::deleting(function ($model) {
            $model->models()->delete();
        });

        MusicPractice::deleting(function ($model) {
            $model->tagPivots()->delete();
        });
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
