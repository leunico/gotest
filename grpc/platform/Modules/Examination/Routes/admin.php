<?php

use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\Registrar as RouteContract;

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
    'middleware' => 'auth:api',
], function (RouteContract $api) {

    // tools
    $api->group(['prefix' => 'tool'], function (RouteContract $api) {
        // 获取考试类型列表
        $api->get('/examination-categroy', 'ToolController@examinationCategroys');
        // 获取考试列表
        $api->get('/examinations', 'ToolController@examinations');
        // 后台考试OJ
        $api->post('/oj', 'ToolController@ojStore');
        // 后台考试OJ运行结果
        $api->get('/oj/{solution}', 'ToolController@ojShow');
    });

    // matches
    $api->group(['prefix' => 'match'], function (RouteContract $api) {
        // 获取赛事列表
        $api->get('/', 'MatchController@index')->middleware('gate:exam_match_menu');
        // 获取赛事信息
        $api->get('/{match}/edit', 'MatchController@edit');
        // 赛事操作集
        $api->group(['middleware' => 'gate:exam_match_edit'], function () use ($api) {
            // 更新赛事信息
            $api->put('/{match}', 'MatchController@update');
            // 删除赛事
            $api->delete('/{match}', 'MatchController@destroy');
            // 创建赛事
            $api->post('/', 'MatchController@store');
        });
    });

    // examinations
    $api->group(['prefix' => 'examination'], function (RouteContract $api) {
        // 获取考试列表
        $api->get('/{matchId?}', 'ExaminationController@index')->middleware('gate:examination_list');
        // 创建考试
        $api->post('/', 'ExaminationController@store')->middleware('gate:examination_edit');
        // 获取考试信息
        $api->get('/{examination}/edit', 'ExaminationController@edit');
        // 考卷发布
        $api->patch('/{examination}/examination-paper', 'ExaminationController@setPaper')->middleware('gate:examination_edit,examination_paper_edit');
        // 考试操作集
        $api->group(['middleware' => 'gate:examination_edit'], function () use ($api) {
            // 更新考试信息
            $api->put('/{examination}', 'ExaminationController@update');
            // 考试发布
            $api->patch('/{examination}/status', 'ExaminationController@setStatus');
            // 考试成绩发布
            $api->patch('/{examination}/publish-results', 'ExaminationController@sePublishResults');
            // 考试资格确认
            $api->patch('/{examination}/qualification', 'ExaminationController@setQualification');
            // 删除考试
            $api->delete('/{examination}', 'ExaminationController@destroy');
        });
    });
});
