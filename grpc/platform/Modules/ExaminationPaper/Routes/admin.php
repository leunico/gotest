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

    // major problem
    $api->group(['prefix' => 'major-problem'], function (RouteContract $api) {
        // 获取大题列表
        $api->get('/{examination}', 'MajorProblemController@index');
        // 获取大题信息
        $api->get('/{problem}/edit', 'MajorProblemController@edit');
        // 大题操作集
        $api->group(['middleware' => 'gate:examination_paper_edit'], function () use ($api) {
            // 创建大题
            $api->post('/', 'MajorProblemController@store');
            // 更新大题信息
            $api->put('/{problem}', 'MajorProblemController@update');
            // 更新大题排序
            $api->patch('/sort', 'MajorProblemController@sort');
            // 更新大题平均分数
            $api->patch('/{problem}/avg-score', 'MajorProblemController@avgScore');
            // 删除大题
            $api->delete('/{problem}', 'MajorProblemController@destroy');
        });
    });

    // question and options
    $api->group(['prefix' => 'question'], function (RouteContract $api) {
        // 获取题目列表（和上面获取大题信息相同）
        // $api->get('/{majorProblem}', 'QuestionController@index');
        // 获取题目信息
        $api->get('/{question}/edit', 'QuestionController@edit')->middleware('gate:examination_paper_list');
        // 题目操作集
        $api->group(['middleware' => 'gate:examination_paper_edit'], function () use ($api) {
            // 创建题目
            $api->post('/', 'QuestionController@store');
            // 更新题目信息
            $api->put('/{question}', 'QuestionController@update');
            // 删除题目
            $api->delete('/{question}', 'QuestionController@destroy');
            // 更新题目排序
            $api->patch('/sort', 'QuestionController@sort');
        });
    });

    // marking
    $api->group(['prefix' => 'marking'], function (RouteContract $api) {
        // 阅卷列表
        $api->get('/{examination}', 'MarkingController@index')->middleware('gate:examination_marking_list');
        // 获取试卷详情
        $api->get('/{eexaminee}/edit', 'MarkingController@edit');
        // 阅卷操作集
        $api->group(['middleware' => 'gate:examination_marking_edit'], function () use ($api) {
            // 阅卷
            $api->post('/', 'MarkingController@store');
            // 修改阅卷分数
            $api->put('/{marking}', 'MarkingController@update');
        });
    });

    // simulation paper
    $api->group(['prefix' => 'simulation'], function (RouteContract $api) {
        // 创建试卷
        $api->post('/', 'ExaminationSimulationPaperController@store')->middleware('gate:examination_paper_edit');
        // 获取试卷信息
        $api->get('/{category}/edit', 'ExaminationSimulationPaperController@edit')->middleware('gate:examination_paper_list');
        // 更新试卷信息
        $api->put('/{simulation}', 'ExaminationSimulationPaperController@update')->middleware('gate:examination_paper_edit');
    });
});
