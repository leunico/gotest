<?php

namespace Modules\Educational\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\User;
use function App\responseSuccess;
use Modules\Educational\Http\Requests\UpdateTeacherPut;
use function App\responseFailed;
use Modules\Educational\Entities\Teacher;
use Illuminate\Support\Facades\DB;
use Modules\Educational\Entities\TeacherOfficeTime;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use App\Rules\ArrayExists;
use Modules\Course\Entities\BiuniqueCourse;
use Modules\Educational\Entities\TeacherCourse;

class TeacherController extends Controller
{
    /**
     * 获取一对一试听老师列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function auditions(Request $request, User $user)
    {
        $perPage = (int) $request->input('per_page', 15);

        $formalCourse = $request->input('formal_course_id', null);
        $auditionCourse = $request->input('audition_course_id', null);
        $keyword = $request->input('keyword', null);
        $type = $request->input('type', null);
        $category = $request->input('category', null);

        $teachers = User::role(Teacher::AUDITION_TEACHER)
            ->select('users.id', 'teachers.id as teacher_id', 'users.name', 'real_name', 'phone', 'sex', 'teachers.type', 'users.created_at')
            ->leftjoin('teachers', 'users.id', 'teachers.user_id')
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('real_name', 'Like', "%$keyword%")
                    ->orWhere('name', 'Like', "%$keyword%")
                    ->orWhere('phone', 'Like', "%$keyword%");
            })
            ->when($formalCourse, function ($query) use ($formalCourse) {
                return $query->whereIn('users.id', function ($query) use ($formalCourse) {
                    return $query->from('teacher_courses')
                        ->select('user_id')
                        ->where('type', TeacherCourse::TYPE_ZS)
                        ->where('biunique_course_id', $formalCourse);
                });
            })
            ->when($auditionCourse, function ($query) use ($auditionCourse) {
                return $query->whereIn('users.id', function ($query) use ($auditionCourse) {
                    return $query->from('teacher_courses')
                        ->select('user_id')
                        ->where('type', TeacherCourse::TYPE_ST)
                        ->where('biunique_course_id', $auditionCourse);
                });
            })
            ->when($category, function ($query) use ($category) {
                return $query->where('teachers.type', $category);
            })
            ->with([
                'teacherCourses' => function ($query) {
                    return $query->select('teacher_courses.id', 'title', 'teacher_courses.sort');
                }
            ])
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return responseSuccess($teachers);
    }

    /**
     * 获取运用教务老师列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function courses(Request $request, User $user)
    {
        $perPage = (int) $request->input('per_page', 15);

        $keyword = $request->input('keyword', null);

        $teachers = User::role(Teacher::COURSE_TEACHER)
            ->select(
                'users.id',
                'teachers.id as teacher_id',
                'users.name',
                'real_name',
                'phone',
                'sex',
                'users.created_at',
                'origin_filename',
                'driver_baseurl',
                'filename'
            )
            ->leftjoin('teachers', 'users.id', 'teachers.user_id')
            ->leftjoin('files', 'teachers.qrcode', 'files.id')
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('real_name', 'Like', "%$keyword%")
                    ->orWhere('phone', 'Like', "%$keyword%");
            })
            ->withCount(['teacherClass'])
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return responseSuccess($teachers);
    }

    /**
     * 获取某个课程某个时间点的老师约课点
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function teacherOfficeTimes(Request $request)
    {
        $type = $request->input('type', TeacherCourse::TYPE_ZS);
        $course = $request->input('course', null);
        $isStatus = $request->input('status', null);
        $date = $request->input('date', null);
        $startTime = $request->input('start_time', null);

        $data = TeacherCourse::where('biunique_course_id', $course)
            ->select(
                'teacher_office_times.sort',
                'teacher_office_times.time',
                'teacher_courses.id as teacher_course_id',
                'teacher_courses.sort as default_sort',
                'teacher_office_times.id',
                'teacher_office_times.status',
                'teacher_courses.user_id'
            )
            ->leftjoin('teacher_office_times', 'teacher_courses.user_id', 'teacher_office_times.user_id')
            ->where('teacher_courses.type', $type)
            ->when($date, function ($query) use ($date) {
                return $query->where('teacher_office_times.appointment_date', $date);
            }, function ($query) use ($startTime) {
                return $query->whereBetween('teacher_office_times.appointment_date', [$startTime, Carbon::parse($startTime)->endOfDay()]);
            })
            ->whereColumn('teacher_office_times.type', 'teacher_courses.type')
            ->when(! is_null($isStatus), function ($query) use ($isStatus) {
                return $query->where('teacher_office_times.status', $isStatus);
            })
            ->with([
                'user' => function ($query) {
                    $query->select('id', 'real_name', 'name');
                }
            ])
            ->orderBy('sort', 'desc')
            ->get();

        return responseSuccess($data);
    }

    /**
     * 获取老师.
     *
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     * @author lizx
     */
    public function edit(User $user)
    {
        $user->load([
            'teacher',
            'teacher.qrcodeFile',
            'teacherCourses' => function ($query) {
                return $query->select('biunique_courses.id', 'title', 'teacher_courses.sort');
            }
        ]);

        return responseSuccess($user);
    }

    /**
     * 设置老师
     *
     * @param \Modules\Educational\Http\Requests\UpdateTeacherPut $request
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(UpdateTeacherPut $request, User $user)
    {
        $teacher = $user->teacher ?? new Teacher;
        $teacher->user_id = $user->id;
        $teacher->type = $request->input('type', 1);
        $teacher->qrcode = $request->input('qrcode', 0);

        if ($teacher->save()) {
            return responseSuccess([
                'teacher_id' => $teacher->id
            ], '设置老师成功');
        } else {
            return responseFailed('设置老师失败', 500);
        }
    }

    /**
     * 设置老师课程
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function syncCourses(Request $request, User $user)
    {
        $this->validate($request, [
            'courses' => [
                'array',
                new ArrayExists(new BiuniqueCourse, false, true)
            ],
            'type' => 'required|in:' . implode(',', array_keys(TeacherCourse::$typeMap)),
        ]);

        $data = array_map(function ($item) use ($request) {
            return ['type' => $request->type];
        }, array_flip($request->courses));

        if ($request->type == TeacherCourse::TYPE_ZS) {
            $user->teacherFormalCourses()->sync($data);
        } elseif ($request->type == TeacherCourse::TYPE_ST) {
            $user->teacherAuditionCourses()->sync($data);
        }

        return responseSuccess();
    }

    /**
     * 获取老师排序列表
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function sorts(Request $request)
    {
        $perPage = (int) $request->input('per_page', 15);

        $type = $request->input('type', TeacherCourse::TYPE_ZS);
        $course = $request->input('course_id', null);

        $data = User::role(Teacher::AUDITION_TEACHER)
            ->select('users.id', 'users.name', 'real_name', 'phone', 'sex', 'users.created_at', 'sort', 'teacher_courses.id as teacher_course_id')
            ->leftjoin('teacher_courses', 'users.id', 'teacher_courses.user_id')
            ->where('teacher_courses.type', $type)
            ->when($course, function ($query) use ($course) {
                $query->where('biunique_course_id', $course);
            })
            ->orderBy('sort', 'desc')
            ->paginate($perPage);

        return responseSuccess($data);
    }

    /**
     * 设置老师的默认排序
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Educational\Entities\TeacherCourse $teacherCourse
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function updateCourseSort(Request $request, TeacherCourse $teacherCourse)
    {
        $this->validate($request, [
            'sort' => [
                'required',
                'integer',
                Rule::unique('teacher_courses')
                    ->where('biunique_course_id', $teacherCourse->biunique_course_id)
                    ->where('type', $teacherCourse->type)
                    ->ignore($teacherCourse->id)
            ],
        ]);

        $teacherCourse->sort = $request->sort;

        if ($teacherCourse->save()) {
            return responseSuccess([
                'teacher_course_id' => $teacherCourse->id
            ], '设置老师的排序成功');
        } else {
            return responseFailed('设置老师的排序失败', 500);
        }
    }

    /**
     * 设置老师的排课排序
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Educational\Entities\TeacherOfficeTime $teacherOfficeTime
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function updateOfficeTimeSort(Request $request, TeacherOfficeTime $teacherOfficeTime)
    {
        $this->validate($request, [
            'sort' => 'required|integer',
        ]);

        $teacherOfficeTime->sort = $request->sort;

        if ($teacherOfficeTime->save()) {
            return responseSuccess([
                'teacher_office_time_id' => $teacherOfficeTime->id
            ], '设置老师的排课排序成功');
        } else {
            return responseFailed('设置老师的排课排序失败', 500);
        }
    }

    /**
     * 获取老师的设置时间
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @param \Modules\Educational\Entities\TeacherOfficeTime $teacherOfficeTime
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function officeTimeEdit(Request $request, User $user, TeacherOfficeTime $teacherOfficeTime)
    {
        $type = $request->input('type', TeacherOfficeTime::TYPE_ZS);
        $startTime = $request->input('start_time', Carbon::now()->startOfWeek());
        $endTime = $request->input('end_time', Carbon::now()->endOfWeek());

        if (! isset(TeacherOfficeTime::$typeMap[$type])) {
            return responseFailed('类型不存在[type]', 422);
        }

        // if (Carbon::parse($endTime)->diffForHumans($startTime, true) != '6 days') {
        //     return responseFailed('时间范围不是一周', 422);
        // }

        $timeList = $teacherOfficeTime->where('user_id', $user->id)
            ->where('type', $type)
            ->whereBetween('appointment_date', [$startTime, (empty($endTime) ? Carbon::now() : Carbon::parse($endTime))->endOfDay()])
            ->get();

        if ($timeList->isEmpty()) {
            $timeList = $teacherOfficeTime->setDefaultTimes($user->id, $type, $startTime);
        }

        return responseSuccess($timeList->groupBy('time')->sortKeys());
    }

    /**
     * 获取全部老师时间
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function officeTimes(Request $request)
    {
        $type = $request->input('type', TeacherCourse::TYPE_ZS);
        $course = $request->input('course', null);
        $startTime = $request->input('start_time', Carbon::now()->startOfWeek());
        $endTime = $request->input('end_time', Carbon::now()->endOfWeek());
        $date = $request->input('date', null);

        $data = TeacherCourse::select(
            'teacher_courses.user_id',
            'teacher_courses.type',
            'teacher_courses.sort as default_sort',
            'biunique_course_id',
            'appointment_date',
            'time',
            'teacher_office_times.sort',
            'teacher_office_times.status',
            'teacher_office_times.id as office_time_id'
        )
            ->leftjoin('teacher_office_times', 'teacher_courses.user_id', 'teacher_office_times.user_id')
            // ->where('status', TeacherOfficeTime::STATUS_OFF) // todo 妈的智障需求
            ->where('teacher_courses.type', $type)
            ->whereColumn('teacher_office_times.type', 'teacher_courses.type')
            ->when($date, function ($query) use ($date) {
                return $query->where('appointment_date', $date);
            }, function ($query) use ($startTime, $endTime) {
                return $query->whereBetween('appointment_date', [$startTime, (empty($endTime) ? Carbon::now() : Carbon::parse($endTime))->endOfDay()]);
            })
            ->when($course, function ($query) use ($course) {
                return $query->where('biunique_course_id', $course);
            })
            ->with([
                'user' => function ($query) {
                    $query->select('id', 'name', 'real_name', 'sex', 'phone', 'created_at');
                }
            ])
            ->orderBy('sort', 'desc')
            ->orderBy('default_sort', 'desc')
            ->orderBy('office_time_id', 'desc')
            ->get()
            ->groupBy('time')
            ->sortKeys();

        return responseSuccess($data);
    }

    /**
     * 设置老师的上班时间
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function storeOfficeTime(Request $request, User $user)
    {
        $this->validate($request, [
            'type' => 'required|in:0,1,2',
            'time' => [
                'required',
                'string',
                'in:' . implode(',', TeacherOfficeTime::ALL_TIMES),
                Rule::unique('teacher_office_times')
                    ->where('user_id', $user->id)
                    ->where('type', empty($request->type) ? TeacherOfficeTime::TYPE_ZS : $request->type)
                    ->where('appointment_date', $request->date . ' ' . str_before($request->time, '-') .':00'),
            ],
            'date' => 'required|date',
        ]);

        $user->getConnection()->transaction(function () use ($request, $user) {
            $types = empty($request->type) ? array_keys(TeacherOfficeTime::$typeMap) : [$request->type];
            foreach ($types as $value) {
                $teacherOfficeTime = new TeacherOfficeTime;
                $teacherOfficeTime->user_id = $user->id;
                $teacherOfficeTime->time = $request->time;
                $teacherOfficeTime->type = $value;
                $teacherOfficeTime->appointment_date = $request->date . ' ' . str_before($request->time, '-');
                $teacherOfficeTime->end_date = $request->date . ' ' . str_after($request->time, '-');
                $teacherOfficeTime->save();
            }
        });

        return responseSuccess([
            'user_id' => $user->id
        ], '设置老师上课时间成功');
    }

    /**
     * 取消某个上班时间
     *
     * @param \Modules\Educational\Entities\TeacherOfficeTime $teacherOfficeTime
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function destroyOfficeTime(TeacherOfficeTime $officeTime)
    {
        if (! empty($officeTime->status)) {
            return responseFailed('已被预约不可取消！');
        }

        $officeTime->delete();

        return responseSuccess();
    }
}
