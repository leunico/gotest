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

        /**
         * 大课程管理
         */
        $api->group(['prefix' => 'big-course'], function (RouteContract $api) {
            // 查看课程列表
            $api->get('/', 'BigCourseController@index')->middleware('role:big_course-index');
            // 添加大课程
            $api->post('/', 'BigCourseController@store');
            // 获取一条大课程
            $api->get('/{course}/edit', 'BigCourseController@edit');
            // 修改一条大课程
            $api->put('/{course}', 'BigCourseController@update');
            // 上下架一条大课程
            $api->patch('/{course}/action', 'BigCourseController@action');
        });

        /**
         * 课程管理
         */
        $api->group(['prefix' => 'course'], function (RouteContract $api) {
            // 查看课程列表
            $api->get('/', 'CourseController@index')->middleware('role:course-index:category');
            // 查看课程课时列表
            $api->get('/course-lessons', 'CourseController@courseLessons');
            // 添加课程
            $api->post('/', 'CourseController@store')->middleware('role:course-store');
            // 获取一条课程
            $api->get('/{course}/edit', 'CourseController@edit')->middleware('role:course-update');
            // 修改一条课程
            $api->put('/{course}', 'CourseController@update');
            // 设置课程的乐理包
            $api->put('/{course}/music-theory', 'CourseController@musicTheory')->middleware('role:course-musicTheory');
            // 设置课程的arduino素材
            $api->put('/{course}/arduino', 'CourseController@arduino')->middleware('role:course-arduino');
            // 上下架一条课程
            $api->patch('/{course}/action', 'CourseController@action')->middleware('role:course-action');
        });

        /**
         * 课程主题管理
         */
        $api->group(['prefix' => 'course-lesson'], function (RouteContract $api) {
            // 查看课程主题列表
            $api->get('/{course_id?}', 'CourseLessonController@index')->middleware('role:course_lesson-index');
            // 添加课程主题
            $api->post('/', 'CourseLessonController@store')->middleware('role:course_lesson-store');
            // 获取一条课程主题
            $api->get('/{lesson}/edit', 'CourseLessonController@edit')->middleware('role:course_lesson-update');
            // 修改一条课程主题
            $api->put('/{lesson}', 'CourseLessonController@update');
            // 上下架一条主题
            $api->patch('/{lesson}/action', 'CourseLessonController@action')->middleware('role:course_lesson-action');
            // 设置一条课程主题的排序
            $api->patch('/{lesson}/sort', 'CourseLessonController@sort');
        });

        /**
         * 课程环节管理
         */
        $api->group(['prefix' => 'course-section'], function (RouteContract $api) {
            // 查看课程环节列表
            $api->get('/{course_id?}', 'CourseSectionController@index')->middleware('role:course_section-index');
            // 添加课程环节
            $api->post('/', 'CourseSectionController@store')->middleware('role:course_section-store');
            // 获取一条课程环节
            $api->get('/{section}/edit', 'CourseSectionController@edit')->middleware('role:course_section-update');
            // 修改一条课程环节
            $api->put('/{section}', 'CourseSectionController@update');
            // 上下架一条课程环节
            $api->patch('/{section}/action', 'CourseSectionController@action')->middleware('role:course_section-action');
            // 设置课程环节的排序【拖拽】
            $api->patch('/sort', 'CourseSectionController@sort');
            // 设置课程环节的排序【上下】
            $api->patch('/{section}/sort', 'CourseSectionController@upDownSort');
        });

        /**
         * 一对一课程管理
         */
        $api->group(['prefix' => 'biunique-course'], function (RouteContract $api) {
            // 查看课程列表
            $api->get('/', 'BiuniqueCourseController@index');
            // 添加课程
            $api->post('/', 'BiuniqueCourseController@store');
            // 获取一条课程
            $api->get('/{course}/edit', 'BiuniqueCourseController@edit');
            // 修改一条课程
            $api->put('/{course}', 'BiuniqueCourseController@update');
            // 上下架一条课程
            $api->patch('/{course}/action', 'BiuniqueCourseController@action');
            // 设置课程的排序【上下】
            $api->patch('/{course}/sort', 'BiuniqueCourseController@upDownSort');
        });

        /**
         * 一对一课时管理
         */
        $api->group(['prefix' => 'biunique-course-lesson'], function (RouteContract $api) {
            // 查看课程课时列表
            $api->get('/{course_id?}', 'BiuniqueCourseLessonController@index');
            // 添加课程课时
            $api->post('/', 'BiuniqueCourseLessonController@store');
            // 获取一条课程课时
            $api->get('/{lesson}/edit', 'BiuniqueCourseLessonController@edit');
            // 修改一条课程课时
            $api->put('/{lesson}', 'BiuniqueCourseLessonController@update');
            // 上下架一条课程课时
            $api->patch('/{lesson}/action', 'BiuniqueCourseLessonController@action');
            // 设置一条课程课时的排序
            $api->patch('/{lesson}/sort', 'BiuniqueCourseLessonController@sort');
            // 设置课程课时的资源
            $api->put('/{lesson}/resource', 'BiuniqueCourseLessonController@resource');
        });

        /**
         * 一对一课程资源管理
         */
        $api->group(['prefix' => 'biunique-course-resource'], function (RouteContract $api) {
            // 查看资源列表
            $api->get('/', 'BiuniqueCourseResourceController@index');
            // 添加资源
            $api->post('/', 'BiuniqueCourseResourceController@store');
            // 获取一条资源
            $api->get('/{resource}/edit', 'BiuniqueCourseResourceController@edit');
            // 修改一条资源
            $api->put('/{resource}', 'BiuniqueCourseResourceController@update');
            // 上下架一条资源
            $api->patch('/{resource}/action', 'BiuniqueCourseResourceController@action');
        });

        /**
         * 一对一课程星星包管理
         */
        $api->group(['prefix' => 'star-package'], function (RouteContract $api) {
            // 查看星星包列表
            $api->get('/', 'StarPackageController@index');
            // 添加星星包
            $api->post('/', 'StarPackageController@store');
            // 获取一条星星包
            $api->get('/{star}/edit', 'StarPackageController@edit');
            // 修改一条星星包
            $api->put('/{star}', 'StarPackageController@update');
            // 上下架一条星星包
            $api->patch('/{star}/action', 'StarPackageController@action');
        });

        /**
         * 题库管理
         */
        $api->group(['prefix' => 'problem'], function (RouteContract $api) {
            // 查看题目列表
            $api->get('/{tag?}', 'ProblemController@index')->middleware('role:problem-index');
            // 添加题目
            $api->post('/', 'ProblemController@store')->middleware('role:problem-store');
            // 获取一条题目
            $api->get('/{problem}/edit', 'ProblemController@edit');
            // 修改一条题目
            $api->put('/{problem}', 'ProblemController@update');
            // 删除题目
            $api->delete('/{problem}', 'ProblemController@destroy')->middleware('role:problem-destroy');
        });

        /**
         * 乐理包管理
         */
        $api->group(['prefix' => 'music-theory'], function (RouteContract $api) {
            // 查看乐理包列表
            $api->get('/', 'MusicTheoryController@index')->middleware('role:music_theory-index');
            // 获取一条乐理包
            $api->get('/{music}/edit', 'MusicTheoryController@edit');
            // 添加乐理包
            $api->post('/', 'MusicTheoryController@store');
            // 修改一条乐理包
            $api->put('/{music}', 'MusicTheoryController@update');
            // 删除一条乐理包
            $api->delete('/{music}', 'MusicTheoryController@destroy');
            // 上下架一条乐理包
            $api->patch('/{music}/action', 'MusicTheoryController@action');
        });

        /**
         * arduino素材管理
         */
        $api->group(['prefix' => 'arduino'], function (RouteContract $api) {
            // 查看arduino素材列表
            $api->get('/', 'ArduinoMaterialController@index')->middleware('role:arduino-index');
            // 添加arduino素材
            $api->post('/', 'ArduinoMaterialController@store');
            // 修改一条arduino素材
            $api->put('/{arduino}', 'ArduinoMaterialController@update');
            // 获取一条arduino素材
            $api->get('/{arduino}/edit', 'ArduinoMaterialController@edit');
            // 删除一条arduino素材
            $api->delete('/{arduino}', 'ArduinoMaterialController@destroy');
            // 设置arduino素材的排序
            $api->patch('/{arduino}/sort', 'ArduinoMaterialController@sort');
        });

        /**
         * 音乐练耳
         */
        $api->group(['prefix' => 'music-practice'], function (RouteContract $api) {
            // 查看音乐练耳列表
            $api->get('/', 'MusicPracticeController@index')->middleware('role:music_practice-index');
            // 添加音乐练耳
            $api->post('/', 'MusicPracticeController@store');
            // 修改一条音乐练耳
            $api->put('/{practice}', 'MusicPracticeController@update');
            // 获取一条音乐练耳
            $api->get('/{practice}/edit', 'MusicPracticeController@edit');
            // 设置标签的排序
            $api->patch('/{practice}/sort', 'MusicPracticeController@sort');
            // 删除一条音乐练耳
            $api->delete('/{practice}', 'MusicPracticeController@destroy');
            // 上下架音乐练耳
            $api->patch('/{practice}/action', 'MusicPracticeController@action');
        });

        /**
         * 标签
         */
        $api->group(['prefix' => 'tag'], function (RouteContract $api) {
            // 查看标签列表
            $api->get('/', 'TagController@index');
            // 添加标签
            $api->post('/', 'TagController@store');
            // 修改一条标签
            $api->put('/{tag}', 'TagController@update');
            // 获取一条音乐练耳
            $api->get('/{tag}/edit', 'TagController@edit');
            // 删除一条标签
            $api->delete('/{tag}', 'TagController@destroy');
            // 设置标签的排序
            $api->patch('/{tag}/sort', 'TagController@sort');
        });
    });
});
