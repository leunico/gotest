<?php

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
//无登录
Route::middleware('jwt')->namespace('Apis')->as('personal_')->group(function () {
    // 作业接口
    Route::prefix('/homework')->as('homework_')->group(function () {
        Route::get('/work-detail/{work}', 'WorkController@workDetail'); //作业详情
        Route::put('/thumbs-up/{work}', 'WorkController@thumbsUp');    //todo 作业点赞不需要登录
        Route::put('/browse/{work}', 'WorkController@browse');    //作业浏览
    });
});


Route::middleware('auth:api')->namespace('Apis')->as('personal_')->group(function () {
    //观看记录
    Route::prefix('class')->as('class')->group(function () {
        //观看记录
        Route::post('learn-record', 'AttendClassController@learnRecord');
        Route::post('watch-record', 'AttendClassController@watchRecord');
    });

    // 作业接口
    Route::prefix('/homework')->as('homework_')->group(function () {
        Route::post('/submit', 'WorkController@submit');  // 作业提交
        Route::post('/save-work', 'WorkController@saveWork'); //arduino作业
        //更新
        Route::put('update-work/{work}', 'WorkController@updateWork'); //更新arduino作业
        //作业列表
        Route::get('work-list', 'WorkController@workList');
        //记录分享作业
        Route::put('share/{work}', 'WorkController@share');
        //生成cdn密钥
        Route::get('/temp-secret', 'TencentCosController@getTempSecret');
        //测试cdn上传
        Route::post('/upload', 'TencentCosController@upload');
        //主题作业列表
        Route::get('lesson-work-list', 'WorkController@lessonWorkList');
        //cnd上传作业
        Route::post('cdn-save-work', 'WorkCosController@cdnSaveWork');
    });

    // 随堂小测相关
    Route::prefix('/small')->as('small')->group(function () {
        Route::post('subject', 'StudyRecordController@submitSubject');    //题目判断
        Route::get('study-report/{lesson}', 'StudyRecordController@studyReport');    //学习报告
    });

    // 课程
    Route::prefix('/course')->as('course')->group(function () {
        Route::get('show', 'CourseInfoController@show');    //题目判断
    });
});

Route::middleware('auth:api')->namespace('Admins')->as('personal_admin_')->group(function () {
    // 用户管理
    Route::prefix('/user-manage')->as('user_manage_')->group(function () {
        // 首页列表
        Route::get('/', 'UserManageController@index')->name('index')->middleware('role:user_manage-index');
        // 设置用户状态
        Route::put('/{user}/status', 'UserManageController@setStatus')->name('set_status')->middleware('role:user_manage-status');
        // 设置用户角色
        Route::patch('/{user}/role', 'UserManageController@setRole')->name('set_role')->middleware('role:user_manage-role');
        // 设置用户权限
        Route::patch('/{user}/premissions', 'UserManageController@setPremissions')->name('set_premissions')->middleware('role:user_manage-premissions');
        // 获取用户的权限
        Route::get('/{user}/premissions', 'UserManageController@premissions')->name('premissions');
        // 获取用户的角色
        Route::get('/{user}/role', 'UserManageController@role')->name('role');
        // 获取用户信息
        Route::get('/{user}/userinfo', 'UserManageController@userinfo')->name('userinfo');
        // 更新用户信息
        Route::put('/{user}/userinfo', 'UserManageController@setUserInfo')->name('set_userinfo')->middleware('role:user_manage-setUserInfo');
        // 创建用户
        Route::post('/user', 'UserManageController@createUser')->name('create_user')->middleware('role:user_manage-createUser');
        // 作业管理列表
        Route::get('/{user}/work', 'UserManageController@work')->name('work');
        // 修改作业状态
        Route::put('/{user}/work/{work}/status', 'UserManageController@setWorkStatus')->name('work_set_status');
        // 课程管理
        Route::get('/{user}/course', 'UserManageController@course')->name('course');
        // 课程详情管理
        Route::get('/{user}/course-learn-record/{course}', 'UserManageController@courseLearnRecord')->name('course_learn_record');
        // 订单管理
        Route::get('/{user}/order', 'UserManageController@order')->name('order');
        // 用户观看录播课数据
        Route::get('/learn-record', 'UserManageController@learnRecord')->name('learn_record')->middleware('role:user_manage-learnRecord');
        // 用户观看录播课数据详情
        Route::get('/learn-record/{user}', 'UserManageController@learnRecordDetail')->name('learn_record_detail');
        // 班级观看录播课数据
        Route::get('/study_class/learn-record', 'ClassManageController@learnRecord')->name('study_class_learn_record')->middleware('role:user_manage-learnRecord');
        // 班级观看录播课数据
        Route::get('/study_class/learn-record/{study_class}', 'ClassManageController@learnRecordDetail')->name('study_class_learn_record_detail')->middleware('role:user_manage-learnRecord');
        // 课程观看录播课数据
        Route::get('/course/learn-record', 'CourseManageController@learnRecord')->name('course_learn_record')->middleware('role:user_manage-learnRecord');
        // 沟通记录列表
        Route::get('/{user}/conversation', 'UserManageController@conversation')->name('conversation');
        // 沟通记录列表
        Route::post('/{user}/conversation', 'UserManageController@createConversation')->name('create_conversation');
        // 寄件记录
        Route::get('/{user}/delivery', 'UserManageController@delivery')->name('delivery');

        //登录记录
        Route::get('{user}/login-logs', 'UserManageController@loginLogs')->name('login_logs');
    });

    // 统计数据
    Route::prefix('/statistics')->as('statistics_')->middleware('role:user_manage-statistics')->group(function () {
        // 用户登录数据统计
        Route::get('/user-login', 'StatisticsController@userLogin')->name('user_login');
        // 交作业数据统计
        Route::get('/homework', 'StatisticsController@homework')->name('homework');
        // 用户学习数据统计
        Route::get('/learn-record', 'StatisticsController@learnRecord')->name('learn_record');
        // 用户数据统计总表
        Route::get('/user-all', 'StatisticsController@userAll')->name('user_all');
        // 用户数据统计总表详情
        Route::get('/user-all-detail', 'StatisticsController@userAllDetail')->name('user_all_detail');
    });
});
