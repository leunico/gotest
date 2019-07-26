<?php

use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\Registrar as RouteContract;
use Illuminate\Support\Facades\Route;

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

Route::group([
    'namespace' => 'Apis',
], function (RouteContract $api) {

    /**
     * 老师以及约课系统[试听]
     */
    $api->group(['prefix' => 'teacher'], function () use ($api) {
        // 查看可预约时间的试听列表
        $api->get('/audition-office-times', 'TeacherController@auditionOfficeTimes');
    });

    /**
     * 预约
     */
    $api->group(['prefix' => 'appointment'], function () use ($api) {
        // 预约一对一课程
        $api->post('/biunique-course-register', 'BiuniqueAppointmentController@storeAndRegister')->middleware('jwt');
    });

    // Must Login
    Route::group([
        'middleware' => [
           'auth:api',
        ],
    ], function () use ($api) {

        /**
         * 预约试听课管理
         */
        $api->group(['prefix' => 'audition_class'], function () use ($api) {
            // 查看预约列表
            $api->get('/', 'AuditionClassController@index');
            // 查看预约详情
            $api->get('/{class}', 'AuditionClassController@show')->where('class', '[0-9]+')->middleware('can:show,class');
        });

        /**
         * 直播
         */
        $api->group(['prefix' => 'live'], function () use ($api) {
            // 获取token
            $api->get('/{appointment}/token', 'LiveChatController@token')->where('appointment', '[0-9]+')->middleware('can:show,appointment');
            // 获取消息token
            $api->get('/{appointment}/msg-token', 'LiveChatController@msgToken')->where('appointment', '[0-9]+')->middleware('can:show,appointment');
        });

        /**
         * 老师以及约课系统
         */
        $api->group(['prefix' => 'teacher'], function () use ($api) {
            // 获取某个老师
            $api->get('/{user}/show', 'TeacherController@show');
            // 查看可预约时间的列表
            $api->get('/office-times', 'TeacherController@officeTimes');
        });

        /**
         * 用户预约相关
         */
        $api->group(['prefix' => 'appointment'], function () use ($api) {
            // 一对一课程
            $api->group(['prefix' => 'biunique-course'], function () use ($api) {
                // 预约一对一课程
                $api->post('/', 'BiuniqueAppointmentController@store');
                // 查看预约记录
                $api->get('/', 'BiuniqueAppointmentController@appointmentLog');
                // 查看预约详情
                $api->get('/{appointment}/show', 'BiuniqueAppointmentController@show')->middleware('can:show,appointment');
                // 查看星星记录
                $api->get('/star', 'BiuniqueAppointmentController@appointmentStarLog');
                // 取消预约
                $api->delete('/{appointment}', 'BiuniqueAppointmentController@destroy');
            });
        });

        /**
         * 工具接口
         */
        $api->group(['prefix' => 'tool'], function () use ($api) {
            // 老师列表
            $api->get('/teachers', 'ToolController@teachers')->name('teachers');
        });
    });
});
