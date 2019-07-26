<?php

use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\Registrar as RouteContract;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'namespace' => 'Admins',
], function (RouteContract $api) {

    // Must Login
    Route::group([
        'middleware' => [
           'auth:api',
        ],
    ], function () use ($api) {

        //todo 权限控制
        $api->group(['prefix' => 'express_users'], function (RouteContract $api) {
            //寄件列表
            $api->get('/', 'DeliveryController@expressUsers');
        });

        $api->group(['prefix' => 'delivery'], function (RouteContract $api) {
            //获取寄件信息
            $api->get('/{expressUser}', 'DeliveryController@getDelivery');
            //添加寄件
            $api->post('add-delivery/{expressUser}', 'DeliveryController@addDelivery');
            //寄件记录列表
            $api->get('get-delivery-list/{expressUser}', 'DeliveryController@getDeliveriesList');
            //寄件完善地址提醒
            $api->post('delivery-reminder', 'DeliveryController@deliveryMessageReminder');
        });

        $api->group(['prefix' => 'user-introduce'], function (RouteContract $api) {
            //获取转介绍用户列表
            $api->get('/', 'UserIntroduceController@index');
            //添加转介绍用户
            $api->post('/', 'UserIntroduceController@store');
            //获取一条转介绍用户
            $api->get('/{introduce}/edit', 'UserIntroduceController@edit');
            //修改转介绍用户
            $api->put('/{introduce}', 'UserIntroduceController@update');
            //分配转介绍用户课程权限
            $api->put('/{user}/course-lesson', 'UserIntroduceController@course');
        });

        $api->group(['prefix' => 'work'], function (RouteContract $api) {
            //学习报告
            $api->get('study-report/{lesson}', 'StudyRecordController@studyReport');
        });

        $api->group(['prefix' => 'investments', 'middleware' => 'role:investment-manage'], function () use ($api) {
            $api->get('/', 'InvestmentController@index');
            $api->post('/', 'InvestmentController@store');
            $api->put('/{investment}', 'InvestmentController@update');
            $api->post('assign-course/{investment}', 'InvestmentController@assignCourses');
            $api->get('/{investment}', 'InvestmentController@show');
        });
    });
});
