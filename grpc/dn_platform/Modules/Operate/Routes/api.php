<?php

use Illuminate\Http\Request;
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

Route::group(['namespace' => 'Apis'], function (RouteContract $api) {
    // Must login
    $api->group(['middleware' => 'auth:api'], function () use ($api) {
        $api->group(['prefix' => 'order'], function (RouteContract $api) {
            // 查看订单
            $api->get('/{order}', 'OrderController@show');
            // 生成课程订单
            // $api->post('course/{type}/wechat', 'OrderController@storeCourseWechat')->where('type', config('wechat.payment_no'));
        });
    });

    // banner列表
    $api->group(['prefix' => 'banner'], function (RouteContract $api) {
        //banner列表
        $api->get('/', 'BannerController@index');
    });

    // 文章
    $api->group(['prefix' => 'article'], function (RouteContract $api) {
        // 文章列表
        $api->get('/', 'ArticleController@index')->name('api.article.index');
        // 文章详情
        $api->get('show/{id}', 'ArticleController@show');
        // 文章浏览
        $api->put('browse/{article}', 'ArticleController@browse');
    });

    // 运营相关
    $api->group(['prefix' => 'promote'], function (RouteContract $api) {
        $api->group(['prefix' => 'article-promote'], function (RouteContract $api) {
            // 获取一条软文推广
            $api->get('/{article}/show', 'ArticlePromoteController@show');
            // 软文推广的pv
            $api->post('/{promote}/pv', 'ArticlePromoteController@setPv');
            // 软文推广的uv
            $api->post('/{promote}/uv', 'ArticlePromoteController@setUv');
        });
    });
});

Route::any('/wechat', 'WechatController@serve');

// 生成课程订单
Route::post('/order/course/{type}/wechat', 'Apis\OrderController@storeCourseWechat')->middleware('jwt')->where('type', config('wechat.payment_no'));

// Route::post('/sms-send', 'SmsController@send')->name('sms_send'); // 已废弃
// Route::post('/{verification}/verificate-code', 'SmsController@verificateCode')->name('verificate_code'); // todo test
