<?php

use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\Registrar as RouteContract;

/*
|--------------------------------------------------------------------------
| ADMIN API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => 'auth:api',
], function (RouteContract $api) {

    // 获取考生模板
    $api->get('/template', 'ExamineeController@template');

    // examinee
    $api->group(['prefix' => 'examinee'], function (RouteContract $api) {
        // 获取考生列表
        $api->get('/{examinationId?}', 'ExamineeController@index')->middleware('gate:examinee_list');
        // 获取考生信息
        $api->get('/{examinee}/edit', 'ExamineeController@edit');
         // 更新考生信息
         $api->put('/{examinee}', 'ExamineeController@update')->middleware('gate:examinee_edit,technical_support_edit');
        // 考生操作集
        $api->group(['middleware' => 'gate:examinee_edit'], function () use ($api) {
            // 创建考生
            $api->post('/', 'ExamineeController@store');
            // 导入Excel文件
            $api->post('/excel', 'ExamineeController@excel');
            // 批量确认考生状态
            $api->patch('/many-status', 'ExamineeController@allStatus');
            // 推送考生检测
            $api->patch('/{eexaminee}/push-testing', 'ExamineeController@pushTesting');
            // 确认取消考生状态
            $api->patch('/{eexaminee}/status', 'ExamineeController@status');
            // 删除考生
            $api->delete('/{eexaminee}', 'ExamineeController@destroy');
        });
    });

    // achievement
    $api->group(['prefix' => 'achievement'], function (RouteContract $api) {
        // 获取考生成绩列表
        $api->get('/{examination}', 'AchievementController@index')->middleware('gate:score_sheet_list');
        // 获取考生成绩详情
        $api->get('/{eexaminee}/show', 'AchievementController@show')->middleware('gate:score_sheet_list');
    });

    // cheat
    $api->group(['prefix' => 'cheat'], function (RouteContract $api) {
        // 防作弊查看
        $api->group(['middleware' => 'gate:examination_cheat_list'], function () use ($api) {
            // 获取防作弊操作详情
            $api->get('/{eexaminee}/show-logs', 'ExamineeCheatController@showLogs');
            // 获取防作弊人脸验证详情
            $api->get('/{eexaminee}/show-faces', 'ExamineeCheatController@showFaces');
            // 获取防作弊录屏详情
            $api->get('/{eexaminee}/show-videos', 'ExamineeCheatController@showVideos');
            // 获取考生防作弊列表
            $api->get('/{examination}', 'ExamineeCheatController@index');
        });
        // 是否作弊状态
        $api->patch('/{eexaminee}/status', 'ExamineeCheatController@status')->middleware('gate:examination_cheat_edit');
    });

    // device probing
    $api->group(['prefix' => 'device-probing'], function (RouteContract $api) {
        // 获取考生设备检测数据
        $api->get('/{eexaminee}', 'ExamineeDeviceProbingController@index');
    });

    // technical support
    $api->group(['prefix' => 'technical-support'], function (RouteContract $api) {
        // 获取技术支持列表
        $api->get('/', 'ExamineeTechnicalSupportController@index')->middleware('gate:technical_support_list');
        // 修改状态
        $api->patch('/{support}/status', 'ExamineeTechnicalSupportController@status')->middleware('gate:technical_support_edit');
    });
});
