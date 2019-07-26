<?php

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

use Illuminate\Contracts\Routing\Registrar as RouteContract;
use Illuminate\Support\Facades\Route;

Route::group([
    'namespace'  => 'Api',
    'middleware' => ['crm'],
], function (RouteContract $api) {
    //渠道来源管理
    $api->group(['prefix' => 'channels'], function (RouteContract $api) {
        $api->get('/', 'ChannelsController@index'); //渠道来源列表
        $api->post('/store', 'ChannelsController@store'); //渠道来源保存
        $api->delete('/destroy', 'ChannelsController@destroy'); //渠道来源删除
    });

    //用户
    $api->group(['prefix' => 'users'], function (RouteContract $api) {
        $api->post('/get_or_create', 'UsersController@getOrCreate');    //用户获取或者创建
        $api->post('/get_users', 'UsersController@getUsers');           //用户获取
    });

    //课程
    $api->group(['prefix' => 'courses'], function (RouteContract $api) {
        $api->post('/sync_courses', 'CoursesController@syncCourses');    //课程同步
    });

    //订单
    $api->group(['prefix' => 'orders'], function (RouteContract $api) {

        //todo 不可用，待完善
//        $api->post('/unpaid', 'OrdersController@unpaid');               //未支付订单
    });
});

Route::namespace('Api')->middleware('auth:api')->name('crm_tool')->as('crm_tool_')->prefix('/tool')->group(function (
    RouteContract $api
) {
    $api->get('/channel', 'ToolController@channel')->name('channel');
});
