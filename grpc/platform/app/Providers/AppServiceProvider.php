<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Factories\Response\ResponseFactory;
use Illuminate\Support\Facades\Schema;
use function App\validateChinaPhoneNumber;
use function App\validateUsername;
use function App\validateDisplayLength;
use function App\validateDisplayWidth;
use function App\isBase64;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;
use Illuminate\Support\Facades\Date;
use Carbon\CarbonImmutable;
use Carbon\Carbon;
use Illuminate\Support\Carbon as IlluminateCarbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // 注册返回标量
        $this->app->singleton(ResponseFactory::class, function () {
            return new ResponseFactory();
        });

        // Date::use(Carbon::class);
        IlluminateCarbon::setToStringFormat(function ($date) {
            return $date->year === 1976 ?
                'jS \o\f F g:i:s a' :
                'jS \o\f F, Y g:i:s a';
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(255);

        // 注册验证规则.
        $this->registerValidator();

        // todo 注册罕见格式
        $guesser = ExtensionGuesser::getInstance();
        $guesser->register(new \App\Guesser\OtherMimeTypeExtensionGuesser);
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

        // 判断字符串是否base64编码
        $this->app->validator->extend('is_base64', function (...$parameters) {
            return isBase64($parameters[1]);
        });
    }
}
