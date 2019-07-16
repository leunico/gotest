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

Route::group([
    'middleware' => 'auth:examinee',
], function (RouteContract $api) {

    // 获取考试内容
    $api->get('/{admissionTicket}', 'ExaminationController@show')
        ->middleware('can:show,' . \Modules\Examination\Entities\Examination::class)
        ->name('examinee-examination-show');

    // 获取考试内容（测试）
    $api->get('/{admissionTicket}/testing', 'ExaminationController@detail')
        ->middleware('can:detail,' . \Modules\Examination\Entities\Examination::class)
        ->name('examinee-examination-detail');

    // 标记开始考试时间
    $api->patch('/{eexaminee}/start', 'ExaminationExamineeControler@start')
        ->middleware('can:middleShow,eexaminee');

    // 获取题目详情
    $api->get('/{eexaminee}/question/{question}', 'ExaminationExamineeControler@question')
        ->middleware('can:questionShow,eexaminee');

    // 添加考生考试操作
    $api->post('/{eexaminee}/operation', 'ExaminationExamineeControler@operation')
        ->middleware('can:middleShow,eexaminee');

    // 考试OJ
    $api->post('/oj/{eexaminee?}', 'OnlineJudgeController@store')
        ->middleware([
            'can:ojShow,' . \Modules\Examination\Entities\ExaminationExaminee::class,
            'throttle:40,1'
        ]);

    // 考试OJ运行结果
    $api->get('/{solution}/oj', 'OnlineJudgeController@show');
});
