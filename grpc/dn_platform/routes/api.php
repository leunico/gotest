<?php

use Illuminate\Contracts\Routing\Registrar as RouteContract;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// web login
Route::post('auth/login', 'AuthController@login')->name('login');

// register.[只用于后台或者调试]
Route::post('register', 'UserController@store')->name('register');

// register.wechat[用于微信的首次登陆]
Route::post('login-wechat', 'AuthController@loginWechat')->name('login_wechat');

// 微信页面、服务号登陆
Route::post('auth/user/{driver}', 'AuthController@socialiteUser');

// 小程序登陆
Route::post('auth/mini-program-login', 'AuthController@miniProgramLogin');

// oauth和回调
Route::group(['prefix' => 'oauth'], function (RouteContract $api) {
    // 微信jssdk
    $api->post('/{type}/jssdk', 'WechatController@jssdk')->where('type', config('wechat.official_account_no'));
    // 微信oauth
    $api->group(['prefix' => 'wechat'], function (RouteContract $api) {
        // 微信网页授权回调
        $api->get('/{type}/web-notify', 'WechatController@webNotify')->where('type', config('wechat.official_account_no'));
    });

    // 支付相关
    $api->group(['prefix' => 'payments'], function (RouteContract $api) {
        // 微信支付回调
        $api->any('/wechat/{type}/notify', 'WechatController@paymentNotify')->where('type', config('wechat.payment_no'))->name('payment.notify');
    });
});

// 年级列表
Route::get('/grade', 'ToolController@grade')->name('grade');

// 发送短信
Route::post('/sms-send', 'SmsController@send')->middleware('throttle:10')->name('sms_send'); //  todo config('services.sms_throttle')

Route::group([
    'middleware' => [
        'auth:api',
    ],
], function (RouteContract $api) {
    // auth
    $api->group(['prefix' => 'auth'], function (RouteContract $api) {
        // logout
        $api->post('logout', 'AuthController@logout')->name('logout');
        // 刷新token
        $api->post('refresh', 'AuthController@refresh')->name('refresh');
        // 获取用户信息
        $api->get('user', 'AuthController@me');
    });

    // files
    $api->group(['prefix' => 'file'], function (RouteContract $api) {
        // 获取文件
        $api->get('/{file}', 'FilesController@show');
        // 上传文件
        $api->post('/', 'FilesController@store');
    });

    // roles
    $api->group(['prefix' => 'role'], function (RouteContract $api) {
        // 角色列表
        $api->get('/', 'RoleController@index');
        // 获取角色
        $api->get('/{role}/edit', 'RoleController@edit')->middleware('role:role-update');
        // 添加角色
        $api->post('/', 'RoleController@store')->middleware('role:role-store');
        // 更新角色
        $api->put('/{role}', 'RoleController@update');
        // 角色设置权限
        $api->put('/{role}/permission', 'RoleController@permission')->middleware('role:role-permission');
        // 删除角色
        $api->delete('/{role}', 'RoleController@destroy')->middleware('role:role-destroy');
    });

    // permissions
    $api->group(['prefix' => 'permission'], function (RouteContract $api) {
        // 权限列表
        $api->get('/', 'PermissionController@index');
        // 添加权限
        $api->post('/', 'PermissionController@store');
    });

    // user
    $api->group(['prefix' => 'user'], function (RouteContract $api) {
        // 更新用户信息
        $api->put('/', 'UserController@update')->name('user_update');
        // 获取用户钱包
        $api->get('/wallet', 'UserController@wallet');
    });

    // admin
    $api->group(['prefix' => 'admin'], function (RouteContract $api) {
        // config
        $api->group(['prefix' => 'setting'], function () use ($api) {
            // 配置列表
            $api->get('/{namespace?}', 'SettingController@index');
            // 更新配置
            $api->put('/{setting}', 'SettingController@update');
        });
    });

    // 更新用户信息
    $api->put('user', 'UserController@update')->name('user_update');

    // 获取全部地区【联动框】
    $api->get('districts', 'ToolController@districts'); // todo web端有数据不需要我给接口？？

    // 默认头像列表
    $api->get('default-head', 'ToolController@defaultHead')->name('default_head');

    // 获取某个配置项目
    $api->get('/setting/{setting}', 'ToolController@setting')->name('setting');
});
