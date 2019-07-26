<?php

declare(strict_types=1);

namespace Modules\Educational\Http\Controllers\Apis;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Educational\Http\Requests\BiuniqueAppointmentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Educational\Entities\TeacherOfficeTime;
use Modules\Educational\Entities\TeacherCourse;
use Modules\Educational\Entities\BiuniqueAppointment;
use Modules\Course\Entities\BiuniqueCourse;
use Illuminate\Support\Carbon;
use Modules\Educational\Transformers\BiuniqueAppointmentRecource;
use Modules\Educational\Http\Requests\RegisterBiuniqueAppointmentRequest;
use Modules\Personal\Events\ChangeUser;
use Illuminate\Support\Facades\Auth;
use App\User;
use Modules\Educational\Entities\Teacher;

class BiuniqueAppointmentController extends Controller
{
    /**
     * 预约一对一课程的记录
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Educational\Entities\BiuniqueAppointment $biuniqueAppointment
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function appointmentLog(Request $request, BiuniqueAppointment $biuniqueAppointment): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);

        $course = $request->input('course', null);
        $startTime = $request->input('start_time', null);
        $endTime = $request->input('end_time', null);
        $type = $request->input('type', 0);
        $date = $request->input('date', null);

        $isTeacher = $this->user()->hasRole(Teacher::AUDITION_TEACHER);
        $data = $biuniqueAppointment->select(
                'biunique_appointments.id',
                'biunique_course_id',
                'teacher_office_times.appointment_date',
                'teacher_office_times.type',
                'end_date',
                'teacher_office_times.user_id as teacher_id',
                'star_cost',
                'attendance',
                'lesson_sort',
                'users.real_name as teacher_name',
                'users.sex as teacher_sex'
            )
            ->leftjoin('teacher_office_times', 'biunique_appointments.teacher_office_time_id', 'teacher_office_times.id')
            ->leftjoin('users', 'teacher_office_times.user_id', 'users.id')
            ->when($isTeacher, function ($query) {
                return $query->where('teacher_office_times.user_id', $this->user()->id)
                    ->where('teacher_office_times.status', TeacherOfficeTime::STATUS_ON);
            }, function ($query) {
                return $query->where('biunique_appointments.user_id', $this->user()->id);
            })
            ->when($course, function ($query) use ($course) {
                return $query->where('biunique_course_id', $course);
            })
            ->when(! is_null($type), function ($query) use ($type) {
                return empty($type) ? $query->where('end_date', '>=', Carbon::now())->orderBy('appointment_date', 'asc') :
                    $query->where('end_date', '<', Carbon::now())->orderBy('appointment_date', 'desc');
            })
            ->when($date, function ($query) use ($date) {
                return $query->whereDate('appointment_date', $date);
            }, function ($query) use ($startTime, $endTime) {
                if ((! empty($startTime) || ! empty($endTime))) {
                    return $query->whereBetween('appointment_date', [$startTime, (empty($endTime) ? Carbon::now() : Carbon::parse($endTime))->endOfDay()]);
                }
            })
            ->with([
                'biuniqueCourse' => function ($query) {
                    $query->select('id', 'category', 'title');
                }
            ])
            ->paginate($perPage);

        collect($data->items())->map(function ($item) use ($isTeacher) {
            $now = Carbon::now();
            $endDate = Carbon::parse($item->end_date);
            if ($isTeacher || ($now->gte(Carbon::parse($item->appointment_date)->subMinutes(15)) && $now->lte($endDate))) {
                $item->appointments_url = config('educational.live.live_web_host') . ($isTeacher ? '?class_id=' : '/stuIndex.html?class_id=') . $item->id; // 高亮【进入课堂】
            } elseif ($now->gte(Carbon::parse($item->appointment_date)->subHour()) && $now->lt(Carbon::parse($item->appointment_date)->subMinutes(15))) {
                $item->appointments_url = 1; // 置灰【进入课堂】
            } elseif ($now->lt(Carbon::parse($item->appointment_date)->subHour())) {
                $item->appointments_url = 0; // 高亮【取消预约】
            } else {
                $item->appointments_url = null; // 记录
            }
        });

        return $this->response()->paginator($data, BiuniqueAppointmentRecource::class, [
            'is_appointment' => (! empty($this->user()->star_amount)) || BiuniqueAppointment::select('id')->where('user_id', $this->user()->id)->get()->isNotEmpty()
        ]);
    }

    /**
     * 预约一对一课程的星星记录
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Educational\Entities\BiuniqueAppointment $biuniqueAppointment
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function appointmentStarLog(Request $request, BiuniqueAppointment $biuniqueAppointment): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);

        $course = $request->input('course', null);
        $startTime = $request->input('start_time', null);
        $endTime = $request->input('end_time', null);
        $teacher = $request->input('teacher', null);

        $data = $biuniqueAppointment->where('biunique_appointments.user_id', $this->user()->id)
            ->select(
                'biunique_appointments.id',
                'biunique_course_id',
                'teacher_office_times.appointment_date',
                'teacher_office_times.user_id as teacher_id',
                'teacher_office_time_id',
                'star_cost',
                'users.real_name as teacher_name'
            )
            ->leftjoin('teacher_office_times', 'biunique_appointments.teacher_office_time_id', 'teacher_office_times.id')
            ->leftjoin('users', 'teacher_office_times.user_id', 'users.id')
            ->when($course, function ($query) use ($course) {
                return $query->where('biunique_course_id', $course);
            })
            ->when($teacher, function ($query) use ($teacher) {
                return $query->where('teacher_office_times.user_id', $teacher);
            })
            ->when((! empty($startTime) || ! empty($endTime)), function ($query) use ($startTime, $endTime) {
                $query->whereBetween('appointment_date', [$startTime, (empty($endTime) ? Carbon::now() : Carbon::parse($endTime))->endOfDay()]);
            })
            ->with([
                'biuniqueCourse' => function ($query) {
                    $query->select('id', 'category', 'title');
                }
            ])
            ->paginate($perPage);

        return $this->response()->paginator($data, BiuniqueAppointmentRecource::class, [
            'all_count_star' => $biuniqueAppointment->where('biunique_appointments.user_id', $this->user()->id)
                ->select('id', 'star_cost')
                ->get()
                ->sum('star_cost'),
        ]);
    }

    /**
     * 预约一对一课程
     *
     * @param \Modules\Educational\Http\Requests\BiuniqueAppointmentRequest $request
     * @param \Modules\Educational\Entities\TeacherOfficeTime $teacherOfficeTime
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(BiuniqueAppointmentRequest $request, TeacherOfficeTime $teacherOfficeTime): JsonResponse
    {
        // todo 此处需注意事务的脏读幻读
        try {
            DB::beginTransaction();
            $teacherOfficeTime->type = $request->input('type', TeacherOfficeTime::TYPE_ZS);
            if (empty($office = $teacherOfficeTime->getRankTeacher(
                $request->appointment_date,
                (int) $request->biunique_course_id,
                $request->teacher,
                (int) $teacherOfficeTime->type
            ))) {
                return $this->response()->error('有人抢在你前面预约了这个时间的最后一个哦！');
            }

            if (! ($biuniqueCourse = BiuniqueCourse::select('id', 'price_star', 'is_audition')
                ->where('id', $request->biunique_course_id)
                ->where('status', BiuniqueCourse::STATUS_ON)
                ->first())) {
                return $this->response()->error('课程不存在啦~');
            }

            if ($teacherOfficeTime->isAudition() && $biuniqueCourse->isNotAudition()) {
                return $this->response()->error('该课程不提供试听~');
            }

            if ($teacherOfficeTime->isFormal() && $biuniqueCourse->price_star > $this->user()->star_amount) {
                return $this->response()->error('你的星星余额不足，当前余额[' . $this->user()->star_amount . ']');
            }

            $biuniqueAppointment = new BiuniqueAppointment;
            $biuniqueAppointment->user_id = $this->user()->id;
            $biuniqueAppointment->teacher_office_time_id = $office->id;
            $biuniqueAppointment->biunique_course_id = $request->biunique_course_id;
            $biuniqueAppointment->star_cost = $teacherOfficeTime->isAudition() ? 0 : $biuniqueCourse->price_star;
            $biuniqueAppointment->remark = (string) $request->input('remark', '');
            $biuniqueAppointment->lesson_sort = $teacherOfficeTime->isFormal() ? $biuniqueAppointment->lastLessonSort($this->user()->id) : 0;
            $office->status = TeacherOfficeTime::STATUS_ON;

            if ($biuniqueAppointment->save() &&
                $office->save() &&
                $this->user()->addUserOrder($biuniqueAppointment, -1, $biuniqueAppointment->star_cost)) {
                DB::commit();
                return $this->response()->success($biuniqueAppointment);
            }

            DB::rollBack();
            return $this->response()->errorServer();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->response()->error($exception->getMessage());
        }
    }

    /**
     * 预约一对一试听课程并且注册[免费预约试听课]
     *
     * @param \Modules\Educational\Http\Requests\RegisterBiuniqueAppointmentRequest $request
     * @param \Modules\Educational\Entities\TeacherOfficeTime $teacherOfficeTime
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function storeAndRegister(RegisterBiuniqueAppointmentRequest $request, TeacherOfficeTime $teacherOfficeTime): JsonResponse
    {
        $user = $request->user();
        try {
            DB::beginTransaction();
            if (empty($user)) {
                $user = $request->unionid ? User::firstOrNew(['unionid' => $request->unionid]) : new User;
                $user->phone = $request->phone;
                $user->real_name = $request->real_name;
                // $user->name = $request->real_name;
                $user->grade = $request->grade;
                $user->createPassword(substr($request->phone, -6));
                if ($user->save()) {
                    $token = Auth::guard('api')->login($user);
                    event(new ChangeUser($user, 'create'));
                } else {
                    return $this->response()->errorServer('用户创建失败');
                }
            }

            if (empty($office = $teacherOfficeTime->getRankTeacher(
                $request->appointment_date,
                (int) $request->biunique_course_id,
                null,
                TeacherOfficeTime::TYPE_ST
            ))) {
                return $this->response()->error('有人抢在你前面预约了这个时间的最后一个哦！');
            }

            $biuniqueAppointment = new BiuniqueAppointment;
            $biuniqueAppointment->user_id = $user->id;
            $biuniqueAppointment->teacher_office_time_id = $office->id;
            $biuniqueAppointment->biunique_course_id = $request->biunique_course_id;
            $biuniqueAppointment->star_cost = 0;
            $biuniqueAppointment->remark = (string) $request->input('remark', '');
            $office->status = TeacherOfficeTime::STATUS_ON;

            if ($biuniqueAppointment->save() && $office->save()) {
                DB::commit();
                return $this->response()->success([
                    'appointment' => $biuniqueAppointment,
                    'access_token' => $token
                ]);
            }

            DB::rollBack();
            return $this->response()->errorServer();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->response()->error($exception->getMessage());
        }
    }

    /**
    * 我的预约课详情.
    *
    * @param \Modules\Educational\Entities\BiuniqueAppointment $biuniqueAppointment
    * @return \Illuminate\Http\JsonResponse
    * @author lizx
    */
    public function show(BiuniqueAppointment $appointment): JsonResponse
    {
        $appointment->load([
            'files',
            'teacherOfficeTime' => function ($query) {
                $query->select('id', 'appointment_date', 'end_date', 'time', 'user_id');
            },
            'teacherOfficeTime.user' => function ($query) {
                $query->select('id', 'name', 'real_name', 'sex');
            },
            'biuniqueCourse' => function ($query) {
                $query->select('id', 'title', 'category', 'introduce');
            },
            'biuniqueCourse.biuniqueLessons' => function ($query) use ($appointment) {
                $query->select('id', 'title', 'biunique_course_id', 'introduce')
                    ->where('biunique_course_id', $appointment->biunique_course_id);
            }
        ]);

        return $this->response()->item($appointment, BiuniqueAppointmentRecource::class);
    }

    /**
     * 取消预约
     *
     * @param \Modules\Educational\Entities\BiuniqueAppointment $biuniqueAppointment
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function destroy(BiuniqueAppointment $appointment): JsonResponse
    {
        try {
            DB::beginTransaction();
            $office = $appointment->teacherOfficeTime;
            $office->status = TeacherOfficeTime::STATUS_OFF;

            if ($this->user()->addUserOrder($appointment, 1, $appointment->star_cost, '学生取消一对一预约') &&
                $appointment->delete() &&
                $office->save()) {
                DB::commit();
                return $this->response()->success();
            }

            DB::rollBack();
            return $this->response()->errorServer('取消失败');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->response()->errorServer($exception->getMessage());
        }
    }
}
