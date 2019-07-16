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

// examinee login
Route::post('login', 'AuthController@Login')->name('examinee-login');

// reset password
Route::post('reset-password', 'AuthController@resetPassword')->name('examinee-reset-password');

// mc解答的json
Route::get('answer/minecraft/project/{answer}', 'ExamineeAnswerController@jsonProject')->name('answer-minecraft-project'); // 因为Mc的要特定的链接

// 解答的json
Route::get('answer/{answer}/project', 'ExamineeAnswerController@jsonProject')->name('answer-project');

/**
 *  Api
 */
Route::group([
    'middleware' => 'auth:examinee'
], function (RouteContract $api) {

    // 人脸核身
    $api->post('/{eexaminee}/liveness-recognition', 'VerificationController@guard')
        ->middleware('can:show,eexaminee');

    // 人脸识别
    $api->post('/{eexaminee}/face-check', 'VerificationController@analysis')
        ->middleware('can:middleShow,eexaminee');

    // 考试录屏
    $api->post('/{eexaminee}/examination-video', 'VerificationController@video')
        ->middleware('can:middleShow,eexaminee');

    // 技术支持
    $api->post('/{eexaminee}/technical-support', 'ExamineeTechnicalSupportController@store')
        ->middleware('can:show,eexaminee'); // 如果要调整，可以把权限放到Request类里面~

    // examinee
    $api->group(['prefix' => 'user'], function (RouteContract $api) {
        // 获取考生信息
        $api->get('/', 'AuthController@userInfo');
        // 更新考生信息
        $api->patch('/password', 'AuthController@updatePassword');
        // logout
        $api->post('logout', 'AuthController@logout')->name('examinee-logout');
        // 刷新token
        $api->post('refresh', 'AuthController@refresh')->name('examinee-refresh');
    });

    // examinee answer
    $api->group(['prefix' => 'answer'], function (RouteContract $api) {
        // 答题
        $api->post('/', 'ExamineeAnswerController@store')->middleware('simulation');
        // 答题[Minecraft]
        $api->post('/minecraft/save', 'ExamineeAnswerController@mcStore')->middleware('simulation');
        // 获取mc解答的project.json
        // $api->get('/minecraft/project/{answer}', 'ExamineeAnswerController@mcProject')->middleware('can:show,answer');
        // 修改解答
        $api->put('/{answer}', 'ExamineeAnswerController@update');
        // 获取最新的答题进度
        $api->get('/{examination}/time', 'ExamineeAnswerController@time');
    });

    // device probings
    $api->group(['prefix' => 'device-probing'], function (RouteContract $api) {
        // 添加检测
        $api->post('/', 'ExamineeDeviceProbingController@store');
        // 修改检测
        $api->put('/{deviceProbing}', 'ExamineeDeviceProbingController@update');
    });

    // 交卷
    $api->post('/{eexaminee}/hand', 'ExamineeAnswerController@handIn')
        ->middleware('can:show,eexaminee'); // todo 交卷时间是否要限制？
});
