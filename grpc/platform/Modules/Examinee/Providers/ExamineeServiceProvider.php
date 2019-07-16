<?php

namespace Modules\Examinee\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Routing\Router;

class ExamineeServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(ModelServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);

        // 路由中间件
        $this->app
            ->make(Router::class)
            ->aliasMiddleware('simulation', \Modules\Examinee\Http\Middleware\ExamineeAnswerSimulationMiddleware::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('examinee.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'examinee'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/examinee');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/examinee';
        }, \Config::get('view.paths')), [$sourcePath]), 'examinee');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/examinee');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'examinee');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'examinee');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
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
