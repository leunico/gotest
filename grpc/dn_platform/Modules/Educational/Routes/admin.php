<?php

use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\Registrar as RouteContract;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
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
         * 老师管理
         */
        $api->group(['prefix' => 'teacher'], function () use ($api) {
            // 查看一对一老师列表
            $api->get('/auditions', 'TeacherController@auditions');
            // 查看运用教务老师列表
            $api->get('/courses', 'TeacherController@courses');
            // 设置一位老师
            $api->put('/{user}', 'TeacherController@update');
            // 获取一位老师详情
            $api->get('/{user}/edit', 'TeacherController@edit');
            // 设置老师的课程权限
            $api->put('/{user}/courses', 'TeacherController@syncCourses');
            // 查看老师排序的列表
            $api->get('/sorts', 'TeacherController@sorts');
            // 设置老师的排序
            $api->patch('/teacher-course-sort/{teacherCourse}', 'TeacherController@updateCourseSort');
            // 设置老师排课的的排序
            $api->patch('/teacher-officeTime-sort/{teacherOfficeTime}', 'TeacherController@updateOfficeTimeSort');
            // 查看指定老师设置时间的列表
            $api->get('/{user}/office-times', 'TeacherController@officeTimeEdit');
            // 查看老师设置时间的列表
            $api->get('/office-times', 'TeacherController@officeTimes');
            // 查看老师某个时间点的老师
            $api->get('/date-office-time', 'TeacherController@teacherOfficeTimes');
            // 添加一位老师的上班时间
            $api->post('/{user}/office-time', 'TeacherController@storeOfficeTime');
            // 删除一条老师的上班时间
            $api->delete('/office-time/{officeTime}', 'TeacherController@destroyOfficeTime');
        });

        /**
         * 学生管理
         */
        $api->group(['prefix' => 'student'], function () use ($api) {
            // 一对一学员
            $api->group(['prefix' => 'biunique'], function () use ($api) {
                // 查看一对一学员列表
                $api->get('/', 'BiuniqueStudentController@index');
                // 查看一对一学员星星列表
                $api->get('/{user}/stars', 'BiuniqueStudentController@stars');
            });
        });


        /**
         * 预约一对一课程
         */
        $api->group(['prefix' => 'biunique-appointment'], function () use ($api) {
            // 查看学员考勤列表
            $api->get('/attendances', 'BiuniqueAppointmentController@attendances');
            // 设置预约的考勤
            $api->patch('/{appointment}/attendance', 'BiuniqueAppointmentController@updateAttendance');
            // 设置预约的课时
            $api->patch('/{appointment}/lesson-sort', 'BiuniqueAppointmentController@updateLessonSort');
            // 设置预约的课时资源
            $api->put('/{appointment}/resources', 'BiuniqueAppointmentController@updateResources');
            // 查看预约的详细信息
            $api->get('/{appointment}/edit', 'BiuniqueAppointmentController@edit');
            // 查看预约列表
            $api->get('/formals', 'BiuniqueAppointmentController@formals');
            // 查看预约点的详情
            $api->get('/formal/show', 'BiuniqueAppointmentController@formalShow');
            // 取消预约
            $api->delete('/formal/{appointment}', 'BiuniqueAppointmentController@destroyFormal');
            // 批量修改老师时间点
            $api->patch('/formal/teacher-office-time', 'BiuniqueAppointmentController@updateFormalOfficeTime');
            // 查看试听预约列表
            $api->get('/auditions', 'BiuniqueAppointmentController@auditions');
            // 添加一条试听预约
            $api->post('/audition', 'BiuniqueAppointmentController@storeAudition');
            // 修改一条试听预约
            $api->put('/audition/{appointment}', 'BiuniqueAppointmentController@updateAudition');
        });

        /**
         * 班级
         */
        $api->group(['prefix' => 'class'], function () use ($api) {
            // 查看班级列表
            $api->get('/', 'ClassController@index');
            // 查看学员列表
            $api->get('/students', 'ClassController@students');
            // 添加班级
            $api->post('/', 'ClassController@store');
            // 获取班级
            $api->get('/{class}/edit', 'ClassController@edit');
            // 获取班级课程
            $api->get('/{class}/course', 'ClassController@courses');
            // 修改班级
            $api->put('/{class}', 'ClassController@update');
            // 取消班级发布
            $api->patch('/{class}/action', 'ClassController@action');
            // 查看班学员列表
            $api->get('/{class}/student', 'ClassController@classStudent');
            // 添加学员
            $api->post('/{class}/student', 'ClassController@addStudent');
            // 删除学员
            $api->delete('/student/{classStudent}', 'ClassController@delStudent');
            // 设置主题解锁
            $api->post('/{class}/course-lesson', 'ClassController@addCourseLessons');
            // 主题解锁上下移动
            $api->patch('/{class}/course-lesson/move', 'ClassController@moveCourseLessons');
        });

        /**
         * 节假日管理
         */
        $api->group(['prefix' => 'holiday'], function () use ($api) {
            // 查看节假日列表
            $api->get('/', 'HolidayController@index');
            // 设置一个节假日
            $api->post('/', 'HolidayController@store');
            // 删除一个节假日
            $api->delete('/{holiday}', 'HolidayController@destroy');
        });

        /**
         * 导出
         */
        $api->group(['prefix' => 'export'], function () use ($api) {
            //导出学员列表
            $api->get('/class/students', 'ExportController@classStudents');
        });
    });
});
