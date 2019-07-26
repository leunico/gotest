<?php

declare(strict_types=1);

namespace Modules\Educational\Http\Controllers\Apis;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Educational\Entities\TeacherCourse;
use Illuminate\Support\Carbon;
use Modules\Educational\Transformers\TeacherCourseResource;
use Illuminate\Http\JsonResponse;
use Modules\Educational\Entities\TeacherOfficeTime;
use App\User;
use Modules\Educational\Transformers\TeacherUserRecource;

class TeacherController extends Controller
{
    /**
     * 获取老师详情
     *
     * @param \App\User $user
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function show(Request $request, User $user): JsonResponse
    {
        $user->load([
            'teacher' => function ($query) {
                $query->select('id', 'type', 'user_id', 'qrcode');
            },
            'teacher.qrcodeFile'
        ]);

        return $this->response()->item($user, TeacherUserRecource::class);
    }

    /**
     * 获取全部的老师时间
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function officeTimes(Request $request): JsonResponse
    {
        $course = $request->input('course', null);
        $teacher = $request->input('teacher', null);
        $startTime = $request->input('start_time', Carbon::now()->startOfWeek());
        $date = $request->input('date', null);

        $data = TeacherOfficeTime::select(
            'teacher_courses.user_id',
            'teacher_courses.sort as default_sort',
            'biunique_course_id',
            'appointment_date',
            'time',
            'teacher_office_times.id',
            'teacher_office_times.sort',
            'status'
        )
            ->rightjoin('teacher_courses', 'teacher_office_times.user_id', 'teacher_courses.user_id')
            ->where('teacher_courses.type', TeacherCourse::TYPE_ZS)
            ->whereColumn('teacher_office_times.type', 'teacher_courses.type')
            ->when($course, function ($query) use ($course) {
                return $query->where('biunique_course_id', $course);
            })
            ->when($teacher, function ($query) use ($teacher) {
                return $query->where('teacher_courses.user_id', $teacher);
            })
            ->when($date, function ($query) use ($date) {
                return $query->whereDate('appointment_date', $date);
            }, function ($query) use ($startTime) {
                return $query->whereBetween('appointment_date', [Carbon::parse($startTime)->startOfWeek(), Carbon::parse($startTime)->endOfWeek()->endOfDay()]);
            })
            ->with([
                'user' => function ($query) {
                    $query->select('id', 'name', 'real_name');
                },
                'biuniqueAppointment' => function ($query) {
                    $query->select('id', 'biunique_course_id', 'teacher_office_time_id')
                        ->where('user_id', $this->user()->id);
                }
            ])
            ->orderBy('sort', 'desc')
            ->orderBy('default_sort', 'desc')
            ->orderBy('teacher_office_times.id', 'desc')
            ->get();

        return $this->response()->collection($data, TeacherCourseResource::class);
    }

    /**
     * 获取试听时间
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function auditionOfficeTimes(Request $request): JsonResponse
    {
        $course = $request->input('course', null);
        $endTime = $request->input('end_time', Carbon::now()->addWeeks(4)->endOfWeek());

        $data = TeacherOfficeTime::select(
            'teacher_courses.user_id',
            'teacher_courses.sort as default_sort',
            'biunique_course_id',
            'appointment_date',
            'time',
            'teacher_office_times.sort',
            'teacher_office_times.id'
        )
            ->rightjoin('teacher_courses', 'teacher_office_times.user_id', 'teacher_courses.user_id')
            ->where('teacher_courses.type', TeacherCourse::TYPE_ST)
            ->where('teacher_office_times.status', TeacherOfficeTime::STATUS_OFF)
            ->whereColumn('teacher_office_times.type', 'teacher_courses.type')
            ->whereBetween('appointment_date', [Carbon::now(), Carbon::parse($endTime)])
            ->when($course, function ($query) use ($course) {
                return $query->where('biunique_course_id', $course);
            })
            ->orderBy('sort', 'desc')
            ->orderBy('default_sort', 'desc')
            ->orderBy('id', 'desc')
            ->groupBy('appointment_date')
            ->get()
            ->map(function ($item) {
                $item->appointment_date = Carbon::parse($item->appointment_date)->toDateString();
                return $item;
            })
            ->groupBy('appointment_date')
            ->sortKeys()
            ->map(function ($item) {
                return $item->sortBy('time')->values();
            });

        return $this->response()->success($data);
    }
}
