<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use function App\validateChinaPhoneNumber;
use function App\validateUsername;
use function App\validateDisplayLength;
use function App\validateDisplayWidth;
use App\Factories\Response\ResponseFactory;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        // 注册验证规则.
        $this->registerValidator();

        \View::share('isMobile', !\Agent::isDesktop());
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        $this->app->singleton(ResponseFactory::class, function () {
            return new ResponseFactory();
        });
    }

    /**
     * 注册验证规则.
     *
     * @return void
     * @author lizx
     */
    protected function registerValidator()
    {
        // 注册中国大陆手机号码验证规则
        $this->app->validator->extend('cn_phone', function (...$parameters) {
            return validateChinaPhoneNumber($parameters[1]);
        });

        // 注册用户名验证规则
        $this->app->validator->extend('username', function (...$parameters) {
            return validateUsername($parameters[1]);
        });

        // 注册显示长度验证规则
        $this->app->validator->extend('display_length', function ($attribute, $value, array $parameters) {
            unset($attribute);

            return validateDisplayLength((string) $value, $parameters);
        });

        // 注册中英文显示宽度验证规则
        $this->app->validator->extend('display_width', function ($attribute, $value, array $parameters) {
            unset($attribute);

            return validateDisplayWidth((string) $value, $parameters);
        });
    }
}
