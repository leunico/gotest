<?php

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
     * 公众号课程
     */
    $api->group(['prefix' => 'course-wechat'], function (RouteContract $api) {
        // 系列课程
        $api->get('/category/{categroy?}', 'CourseController@wechatIndex')->middleware('jwt')->where('categroy', '1|2')->name('course-wechat-list'); //可能登陆
        // 课程详情
        $api->get('/{course}', 'CourseController@wechatShow')->where('course', '[0-9]+')->name('course-wechat-show');
    });

    /**
     * 公众号大课程
     */
    $api->group(['prefix' => 'big-course-wechat'], function (RouteContract $api) {
        // 大课程详情
        $api->get('/{course}', 'BigCourseController@wechatShow')->where('course', '[0-9]+')->name('big-course-wechat-show');
    });

    /**
     * 工具接口【非必须登陆】
     */
    $api->group(['prefix' => 'tool'], function (RouteContract $api) {
        // 一对一课程列表
        $api->get('/biunique-courses', 'ToolController@biuniqueCourse')->middleware('jwt')->name('biunique_course');
    });

    // Must Login
    Route::group([
        'middleware' => [
            'auth:api',
        ],
    ], function (RouteContract $api) {

        /**
         * 课程
         */
        $api->group(['prefix' => 'course'], function (RouteContract $api) {
            // 购买的课程
            $api->get('/user/{categroy?}', 'CourseController@list')->where('categroy', '1|2')->name('course-user');
            // 课程首页详情
            $api->get('/{course}', 'CourseController@show')
                ->where('course', '[0-9]+')
                ->middleware('can:show,course')
                ->name('course-show');
        });

        /**
         * 课程主题学习
         */
        $api->group(['prefix' => 'course-lesson'], function (RouteContract $api) {
            // 上课页面
            $api->get('/{lesson}', 'CourseLessonController@show')
                ->where('lesson', '[0-9]+')
                ->middleware('can:show,lesson')
                ->name('course-lesson-show');
        });

        /**
         * 课程主题下的环节
         */
        $api->group(['prefix' => 'course-section'], function (RouteContract $api) {
            // 获取环节的题目
            $api->get('/{section}/problems', 'CourseSectionController@problems')
                ->where('section', '[0-9]+')
                // ->middleware('can:show,section') // todo 这里的权限是否要？
                ->name('course-section-problems');
        });

        /**
         * 乐理包学习
         */
        $api->group(['prefix' => 'music-theory'], function (RouteContract $api) {
            // 上课页面
            $api->get('/{course}', 'MusicTheoryController@show')
                ->where('course', '[0-9]+')
                ->middleware('can:show,course')
                ->name('music-theory-show');
        });

        /**
         * 音乐练耳
         */
        $api->group(['prefix' => 'music-practice'], function (RouteContract $api) {
            // 列表页面
            $api->get('/', 'MusicPracticeController@index')->name('music-practice-index');
            // 上课页面
            $api->get('/{practice}', 'MusicPracticeController@show');
        });

        /**
         * 获取素材
         */
        $api->group(['prefix' => 'material'], function (RouteContract $api) {
            // 获取上课arduino素材
            $api->get('/arduinos/{course}', 'ArduinoMaterialController@arduinos')->middleware('can:show,course');
        });

        /**
         * 工具接口
         */
        $api->group(['prefix' => 'tool', 'name' => 'course_tool'], function (RouteContract $api) {
            // 课程列表
            $api->get('/course', 'ToolController@course')->name('course');
            // 标签列表
            $api->get('/tags', 'ToolController@tags')->name('tag');
        });
    });
});
