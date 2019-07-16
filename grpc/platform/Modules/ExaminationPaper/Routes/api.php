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

// question
Route::group(['prefix' => 'question'], function (RouteContract $api) {
    // 获取预加载json数据
    $api->get('/code-json/{question}', 'QuestionController@codeJson');
});

Route::group([
    'middleware' => 'auth:examinee',
], function (RouteContract $api) {

    // question
    // $api->group(['prefix' => 'question'], function (RouteContract $api) {
    //     // 获取预加载json数据
    //     $api->get('/code-json/{question}', 'QuestionController@codeJson');
    // });

    // simulation paper
    $api->group(['prefix' => 'simulation'], function (RouteContract $api) {
        // 获取针对类型的模拟试卷
        $api->get('/{category}/show', 'ExaminationSimulationPaperController@show');
    });
});