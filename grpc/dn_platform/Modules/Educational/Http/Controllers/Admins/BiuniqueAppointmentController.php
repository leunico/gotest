<?php

namespace Modules\Educational\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Educational\Entities\TeacherOfficeTime;
use function App\responseSuccess;
use Illuminate\Support\Carbon;
use Modules\Educational\Entities\TeacherCourse;
use Modules\Educational\Entities\BiuniqueAppointment;
use function App\responseFailed;
use Illuminate\Support\Facades\DB;
use App\Rules\ArrayExists;
use Modules\Course\Entities\BiuniqueCourse;
use Modules\Educational\Http\Requests\StoreAuditionClassPost;
use Illuminate\Validation\Rule;
use App\File;
use App\User;
use Modules\Course\Entities\BiuniqueCourseResource;

class BiuniqueAppointmentController extends Controller
{
    /**
     * 获取正式课的预约
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     * @author lizx
     */
    public function formals(Request $request)
    {
        $course = $request->input('course', null);
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
            ->when($date, function ($query) use ($date) {
                return $query->whereDate('appointment_date', $date);
            }, function ($query) use ($startTime) {
                return $query->whereBetween('appointment_date', [$startTime, Carbon::parse($startTime)->endOfWeek()->endOfDay()]);
            })
            ->with([
                'biuniqueAppointment' => function ($query) {
                    $query->select('id', 'teacher_office_time_id', 'biunique_course_id');
                }
            ])
            ->orderBy('sort', 'desc')
            ->orderBy('default_sort', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('time')
            ->sortKeys();

        return responseSuccess($data);
    }

    /**
     * 获取预约的试听课
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     * @author lizx
     */
    public function auditions(Request $request)
    {
        $perPage = (int) $request->input('per_page', 15);

        $course = $request->input('course', null);
        $status = $request->input('status', null);
        $keyword = $request->input('keyword', null);

        $data = BiuniqueAppointment::select(
            'biunique_appointments.id',
            'biunique_appointments.user_id',
            'biunique_course_id',
            'biunique_appointments.created_at',
            'appointment_date',
            'end_date',
            'teacher_office_times.id as office_time_id',
            'remark',
            'users.real_name as teacher_name'
        )
            ->withTrashed()
            ->leftjoin('teacher_office_times', 'biunique_appointments.teacher_office_time_id', 'teacher_office_times.id')
            ->leftjoin('users', 'teacher_office_times.user_id', 'users.id')
            ->whereNull('biunique_appointments.deleted_at')
            ->where('teacher_office_times.type', TeacherOfficeTime::TYPE_ST)
            ->where('teacher_office_times.status', TeacherOfficeTime::STATUS_ON)
            ->when($course, function ($query) use ($course) {
                return $query->where('biunique_course_id', $course);
            })
            ->when($keyword, function ($query) use ($keyword) {
                return $query->whereIn('biunique_appointments.user_id', function ($query) use ($keyword) {
                    return $query->from('users')
                        ->select('id')
                        ->where('real_name', 'LIKE', "%$keyword%")
                        ->orWhere('name', 'LIKE', "%$keyword%")
                        ->orWhere('phone', 'LIKE', "%$keyword%");
                });
            })
            ->when(! is_null($status), function ($query) use ($status) {
                if ($status == BiuniqueAppointment::STATUS_OVER) {
                    return $query->where('end_date', '<', Carbon::now()); // 2已完成
                } elseif ($status == BiuniqueAppointment::STATUS_NO) {
                    return $query->where('appointment_date', '<=', Carbon::now())
                        ->where('end_date', '>=', Carbon::now()); // 1上课中
                } elseif ($status == BiuniqueAppointment::STATUS_OFF) {
                    return $query->where('appointment_date', '>', Carbon::now()); // 0等待上课
                } else {
                    return $query->whereNotNull('deleted_at'); // -1取消预约
                }
            })
            ->with([
                'user' => function ($query) {
                    $query->select('id', 'name', 'real_name', 'phone');
                },
                'creator',
                'biuniqueCourse' => function ($query) {
                    $query->select('id', 'title');
                }
            ])
            ->orderBy('biunique_appointments.id', 'desc')
            ->get()
            ->map(function ($item) {
                $item->appointments_url = config('educational.live.live_web_host') . '?class_id=' . $item->id;
                if (Carbon::parse($item->appointment_date)->gt(Carbon::now())) {
                    $item->status = BiuniqueAppointment::STATUS_OFF;
                } elseif (Carbon::parse($item->end_date)->lt(Carbon::now())) {
                    $item->status = BiuniqueAppointment::STATUS_OVER;
                } elseif (Carbon::parse($item->appointment_date)->lte(Carbon::now()) && Carbon::parse($item->end_date)->gte(Carbon::now())) {
                    $item->status = BiuniqueAppointment::STATUS_NO;
                } else {
                    $item->status = -1;
                }
                return $item;
            });

        return responseSuccess($data);
    }

    /**
     * 预约试听课
     *
     * @param \Modules\Educational\Http\Requests\StoreAuditionClassPost $request
     * @param \Modules\Educational\Http\Requests\TeacherOfficeTime $teacherOfficeTime
     * @param \Modules\Educational\Entities\BiuniqueAppointment $appointment
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function storeAudition(StoreAuditionClassPost $request, BiuniqueAppointment $appointment, TeacherOfficeTime $teacherOfficeTime)
    {
        try {
            DB::beginTransaction();
            if (empty($office = TeacherOfficeTime::where('id', $request->teacher_office_time_id)
                ->where('status', TeacherOfficeTime::STATUS_OFF)
                ->where('type', TeacherOfficeTime::TYPE_ST)
                ->first())) {
                return responseFailed('您指定的老师没空，并不想鸟你。');
            }

            if ($office->appointment_date != $request->appointment_date) {
                return responseFailed('您指定的老师和预约的时间不相同！');
            }

            $appointment->user_id = $request->user_id;
            $appointment->teacher_office_time_id = $request->teacher_office_time_id;
            $appointment->creator_id = $this->user()->id;
            $appointment->biunique_course_id = $request->biunique_course_id;
            $appointment->remark = (string) $request->input('remark', '');
            $office->status = TeacherOfficeTime::STATUS_ON;

            if ($appointment->save() && $office->save()) {
                DB::commit();
                return responseSuccess($appointment);
            }

            DB::rollBack();
            return responseFailed('预约失败', 500);
        } catch (\Exception $exception) {
            DB::rollBack();
            return responseFailed($exception->getMessage());
        }
    }

    /**
     * 修改预约的试听课
     *
     * @param \Modules\Educational\Http\Requests\StoreAuditionClassPost $request
     * @param \Modules\Educational\Http\Requests\TeacherOfficeTime $teacherOfficeTime
     * @param \Modules\Educational\Entities\BiuniqueAppointment $appointment
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function updateAudition(StoreAuditionClassPost $request, BiuniqueAppointment $appointment, TeacherOfficeTime $teacherOfficeTime)
    {
        try {
            DB::beginTransaction();
            if ($appointment->teacher_office_time_id != $request->teacher_office_time_id) {
                if (empty($office = TeacherOfficeTime::where('id', $request->teacher_office_time_id)
                    ->where('status', TeacherOfficeTime::STATUS_OFF)
                    ->where('type', TeacherOfficeTime::TYPE_ST)
                    ->first())) {
                    return responseFailed('您指定的老师没空，并不想鸟你。');
                }

                if ($office->appointment_date != $request->appointment_date) {
                    return responseFailed('您指定的老师和预约的时间不相同！');
                }

                $oldOffice = $appointment->teacherOfficeTime;
                $office->status = TeacherOfficeTime::STATUS_ON;
                $oldOffice->status = TeacherOfficeTime::STATUS_OFF;
                $appointment->teacher_office_time_id = $request->teacher_office_time_id;

                if ($appointment->save() && $office->save() && $oldOffice->save()) {
                    DB::commit();
                    return responseSuccess($appointment);
                }
            }

            DB::rollBack();
            return responseFailed('修改预约失败', 500);
        } catch (\Exception $exception) {
            DB::rollBack();
            return responseFailed($exception->getMessage());
        }
    }

    /**
     * 获取某个时间点的预约学生[预约详情]
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     * @author lizx
     */
    public function formalShow(Request $request)
    {
        $course = $request->input('course', null);
        $date = $request->input('date', null);

        $data = BiuniqueAppointment::select(
            'biunique_appointments.id',
            'biunique_appointments.user_id',
            'biunique_course_id',
            'appointment_date',
            'teacher_office_times.id as office_time_id',
            'remark',
            'lesson_sort',
            'users.real_name as teacher_name'
        )
            ->leftjoin('teacher_office_times', 'biunique_appointments.teacher_office_time_id', 'teacher_office_times.id')
            ->leftjoin('users', 'teacher_office_times.user_id', 'users.id')
            ->where('teacher_office_times.type', TeacherOfficeTime::TYPE_ZS)
            ->where('teacher_office_times.status', TeacherOfficeTime::STATUS_ON)
            ->where('biunique_course_id', $course)
            ->where('appointment_date', $date)
            ->with([
                'user' => function ($query) {
                    $query->select('id', 'name', 'real_name', 'grade', 'phone');
                },
                'biuniqueCourse' => function ($query) {
                    $query->select('id', 'title', 'category');
                },
                'biuniqueCourse.biuniqueLessons' => function ($query) {
                    $query->select('id', 'title', 'sort', 'biunique_course_id');
                }
            ])
            ->get()
            ->map(function ($item) {
                $item->biuniqueLesson = $item->biuniqueCourse->biuniqueLessons->where('sort', $item->lesson_sort)->first();
                return $item;
            });

        return responseSuccess($data);
    }

    /**
     * 取消预约
     *
     * @param \Modules\Educational\Entities\BiuniqueAppointment $biuniqueAppointment
     * @return Illuminate\Http\Response
     * @author lizx
     */
    public function destroyFormal(BiuniqueAppointment $appointment)
    {
        try {
            DB::beginTransaction();
            $office = $appointment->teacherOfficeTime;
            $office->status = TeacherOfficeTime::STATUS_OFF;

            if (User::find($appointment->user_id)->addUserOrder($appointment, 1, $appointment->star_cost, '取消一对一预约') &&
                $appointment->delete() &&
                $office->save()) {
                DB::commit();
                return responseSuccess();
            }

            DB::rollBack();
            return responseFailed('取消失败', 500);
        } catch (\Exception $exception) {
            DB::rollBack();
            return responseFailed($exception->getMessage());
        }
    }

    /**
     * 更换老师
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     * @author lizx
     */
    public function updateFormalOfficeTime(Request $request)
    {
        $this->validate($request, ['appointments' => [
            'required',
            'array',
            new ArrayExists(new TeacherOfficeTime, true)
        ]]);

        try {
            DB::beginTransaction();
            $addOffice = $subOffice = [];
            $appointments = collect($request->appointments);
            $appointments->map(function ($value, $key) use (&$addOffice, &$subOffice) {
                if (empty($value)) {
                    $subOffice[] = $key;
                } else {
                    $addOffice[] = $key;
                }
            });
            $appointments = $appointments->flip();
            TeacherOfficeTime::whereIn('id', $subOffice)->update(['status' => TeacherOfficeTime::STATUS_OFF]);
            TeacherOfficeTime::whereIn('id', $addOffice)->update(['status' => TeacherOfficeTime::STATUS_ON]);
            BiuniqueAppointment::whereIn('id', $appointments->keys())
                ->select('id', 'teacher_office_time_id', 'user_id')
                ->get()
                ->map(function ($item) use ($appointments) {
                    if ($appointments->get($item->id) && $item->teacher_office_time_id != $appointments->get($item->id)) {
                        $item->teacher_office_time_id = $appointments->get($item->id);
                        $item->save();
                    }
                });

            DB::commit();
            return responseSuccess('更换成功');
        } catch (\Exception $exception) {
            DB::rollBack();
            return responseFailed($exception->getMessage());
        }
    }

    /**
     * 获取一对一预约详细.
     *
     * @param \Modules\Educational\Entities\BiuniqueAppointment $appointment
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(BiuniqueAppointment $appointment)
    {
        $appointment->load([
            'files',
            'creator',
            'biuniqueCourse' => function ($query) {
                $query->select('id', 'title', 'category', 'price_star');
            },
            'user' => function ($query) {
                $query->select('id', 'name', 'real_name');
            },
            'teacherOfficeTime' => function ($query) {
                $query->select('id', 'user_id', 'time', 'appointment_date', 'end_date', 'type');
            },
            'teacherOfficeTime.user' => function ($query) {
                $query->select('id', 'name', 'real_name');
            }
        ]);

        return responseSuccess($appointment);
    }

    /**
     * 设置预约课时
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Educational\Entities\BiuniqueAppointment $appointment
     * @return Illuminate\Http\Response
     * @author lizx
     */
    public function updateLessonSort(Request $request, BiuniqueAppointment $appointment)
    {
        $this->validate($request, [
            'sort' => [
                'required',
                'integer',
                Rule::exists('biunique_course_lessons')
                    ->where('biunique_course_id', $appointment->biunique_course_id)
                    ->whereNull('deleted_at')
            ],
        ]);

        $appointment->lesson_sort = $request->sort;
        return $appointment->save() ? responseSuccess() : responseFailed('设置预约课时失败', 500);
    }

    /**
     * 设置预约课时
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Educational\Entities\BiuniqueAppointment $appointment
     * @return Illuminate\Http\Response
     * @author lizx
     */
    public function updateResources(Request $request, BiuniqueAppointment $appointment)
    {
        $this->validate($request, [
            'resources' => [
                'array',
                new ArrayExists(new File, true)
            ],
        ]);

        $appointment->files()->sync(array_map(function ($item) {
            return ['resource_name' => $item];
        }, $request->resources));

        return responseSuccess();
    }

    /**
     * 考勤退款
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Educational\Entities\BiuniqueAppointment $appointment
     * @return Illuminate\Http\Response
     * @author lizx
     */
    public function updateAttendance(Request $request, BiuniqueAppointment $appointment)
    {
        $this->validate($request, [
            'attendance' => 'required|in:' . implode(',', array_keys(BiuniqueAppointment::$attendanceMap)),
            'status' => 'in:0,1|required_if:attendance,' . BiuniqueAppointment::ATTENDANCE_LOSE
        ]);

        if ($appointment->teacherOfficeTime && Carbon::parse($appointment->teacherOfficeTime->end_date)->gte(Carbon::now())) {
            return responseFailed('上课还没结束呢！');
        }

        try {
            DB::beginTransaction();
            $appointment->attendance = $request->attendance;
            if ($request->attendance == BiuniqueAppointment::ATTENDANCE_LOSE && ! empty($request->status)) {
                User::find($appointment->user_id)->addUserOrder($appointment, 1, $appointment->star_cost, '考勤退款');
                $appointment->star_cost = 0;
            }
            if ($appointment->save()) {
                DB::commit();
                return responseSuccess();
            }

            DB::rollBack();
            return responseFailed('设置失败', 500);
        } catch (\Exception $exception) {
            DB::rollBack();
            return responseFailed($exception->getMessage());
        }
    }

    /**
     * 获取学员考勤列表
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     * @author lizx
     */
    public function attendances(Request $request)
    {
        $perPage = (int) $request->input('per_page', 15);

        $courseCategory = $request->input('course_category', null);
        $course = $request->input('course', null);
        $lessonSort = $request->input('lesson', null);
        $teacher = $request->input('teacher', null);
        $keyword = $request->input('keyword', null);

        $data = BiuniqueAppointment::select(
            'biunique_appointments.id',
            'biunique_appointments.user_id',
            'biunique_appointments.lesson_sort',
            'attendance',
            'star_cost',
            'biunique_course_id',
            'appointment_date',
            'end_date',
            'teacher_office_times.id as office_time_id',
            'users.real_name as teacher_name',
            'users.id as teacher_id'
        )
            ->leftjoin('teacher_office_times', 'biunique_appointments.teacher_office_time_id', 'teacher_office_times.id')
            ->leftjoin('users', 'teacher_office_times.user_id', 'users.id')
            ->where('end_date', '<=', Carbon::now())
            ->where('teacher_office_times.type', TeacherOfficeTime::TYPE_ZS)
            ->where('teacher_office_times.status', TeacherOfficeTime::STATUS_ON)
            ->when($courseCategory, function ($query) use ($courseCategory) {
                return $query->whereIn('biunique_appointments.biunique_course_id', function ($query) use ($courseCategory) {
                    return $query->from('biunique_courses')
                        ->select('id')
                        ->where('status', BiuniqueCourse::STATUS_ON)
                        ->where('category', $courseCategory);
                });
            })
            ->when($course, function ($query) use ($course) {
                return $query->where('biunique_course_id', $course);
            })
            ->when($lessonSort, function ($query) use ($lessonSort) {
                return $query->where('lesson_sort', $lessonSort);
            })
            ->when($teacher, function ($query) use ($teacher) {
                return $query->where('teacher_office_times.user_id', $teacher);
            })
            ->when($keyword, function ($query) use ($keyword) {
                return $query->whereIn('biunique_appointments.user_id', function ($query) use ($keyword) {
                    return $query->from('users')
                        ->select('id')
                        ->where('real_name', 'LIKE', "%$keyword%")
                        ->orWhere('name', 'LIKE', "%$keyword%")
                        ->orWhere('phone', 'LIKE', "%$keyword%");
                });
            })
            ->with([
                'user' => function ($query) {
                    $query->select('id', 'name', 'real_name', 'phone', 'grade', 'sex');
                },
                'biuniqueCourse' => function ($query) {
                    $query->select('id', 'title', 'category');
                },
                'biuniqueCourse.biuniqueLessons' => function ($query) {
                    $query->select('id', 'title', 'sort', 'biunique_course_id');
                }
            ])
            ->orderBy('appointment_date', 'desc')
            ->paginate($perPage);

        collect($data->items())->map(function ($item) {
            $item->biuniqueLesson = $item->biuniqueCourse->biuniqueLessons->where('sort', $item->lesson_sort)->first();
            $item->is_action = $this->user()->isSuperAdmin() ||
                $this->user()->can('biunique-attendance-action') ||
                (Carbon::now()->gte(Carbon::parse($item->end_date)) && Carbon::now()->lte(Carbon::parse($item->end_date)->addHours(3)));
            return $item;
        });

        return responseSuccess($data, 'Success.', ['courseCategoryMap' => BiuniqueCourse::$categoryMap]);
    }
}
