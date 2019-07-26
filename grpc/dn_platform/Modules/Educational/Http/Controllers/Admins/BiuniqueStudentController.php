<?php

namespace Modules\Educational\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Operate\Entities\StarPackageUser;
use function App\responseSuccess;
use App\User;
use Modules\Educational\Entities\BiuniqueAppointment;
use Modules\Course\Entities\BiuniqueCourse;
use Modules\Educational\Entities\TeacherOfficeTime;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Educational\Exports\BiuniqueStudentsExport;
use Illuminate\Support\Carbon;
use Modules\Educational\Exports\BiuniqueAppointmentStarsExport;
use Modules\Course\Entities\BiuniqueCourseLesson;

class BiuniqueStudentController extends Controller
{
    /**
     * 一对一学员列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Operate\Entities\StarPackageUser $students
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, StarPackageUser $students)
    {
        $perPage = (int) $request->input('per_page', 15);

        $grade = $request->input('grade', null);
        $sex = $request->input('sex', null);
        $keyword = $request->input('keyword', null);
        $orderAmount = $request->input('order_amount', null);
        $orderCost = $request->input('order_cost', null);

        $data = $students->select(
            'users.id as user_id',
            'name',
            'real_name',
            'phone',
            'grade',
            'sex',
            'star_amount',
            'star_package_users.id as star_package_user_id',
            DB::raw('(select sum(star_cost) from biunique_appointments where biunique_appointments.user_id = users.id and deleted_at is null) as appointment_stars')
        )
            ->rightjoin('users', 'star_package_users.user_id', 'users.id')
            ->whereNotNull('star_package_users.id')
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('real_name', 'Like', "%$keyword%")
                    ->orWhere('name', 'Like', "%$keyword%")
                    ->orWhere('phone', 'Like', "%$keyword%");
            })
            ->when(! is_null($orderAmount), function ($query) use ($orderAmount) {
                return $query->orderBy('star_amount', empty($orderAmount) ? 'asc' : 'desc');
            })
            ->when(! is_null($orderCost), function ($query) use ($orderCost) {
                return $query->orderBy('appointment_stars', empty($orderCost) ? 'asc' : 'desc');
            })
            ->when($sex, function ($query) use ($sex) {
                return $query->where('sex', $sex);
            })
            ->when($grade, function ($query) use ($grade) {
                return $query->where('grade', $grade);
            })
            ->groupBy('user_id')
            ->orderBy('star_package_user_id', 'desc');

        if ($request->export) {
            return Excel::download(new BiuniqueStudentsExport($data->get()), Carbon::now()->format('Y-m-d H:i:s') . '一对一学员列表.xlsx');
        }

        return responseSuccess($data->paginate($perPage), 'Success.', ['gradeMap' => User::$gradeMap]);
    }

    /**
     * 一对一学员星星列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function stars(Request $request, User $user)
    {
        $perPage = (int) $request->input('per_page', 15);

        $course = $request->input('course', null);
        $teacher = $request->input('teacher', null);
        $keyword = $request->input('keyword', null);

        $data = BiuniqueAppointment::select(
            'biunique_appointments.id',
            'biunique_appointments.user_id',
            'biunique_course_id',
            'appointment_date',
            'star_cost',
            'lesson_sort',
            'users.real_name as teacher_name',
            'teacher_office_times.user_id as teacher_id',
            'phone'
        )
            ->leftjoin('teacher_office_times', 'biunique_appointments.teacher_office_time_id', 'teacher_office_times.id')
            ->leftjoin('users', 'teacher_office_times.user_id', 'users.id')
            ->where('teacher_office_times.status', TeacherOfficeTime::STATUS_ON)
            ->where('biunique_appointments.user_id', $user->id)
            ->when($course, function ($query) use ($course) {
                return $query->where('biunique_course_id', $course);
            })
            ->when($teacher, function ($query) use ($teacher) {
                return $query->where('teacher_office_times.user_id', $teacher);
            })
            ->when($keyword, function ($query) use ($keyword) {
                $lessons = BiuniqueCourseLesson::select('biunique_course_id', 'sort')
                    ->where('title', 'LIKE', "%$keyword%")
                    ->get();

                return $query->whereIn('biunique_course_id', $lessons->pluck('biunique_course_id'))
                    ->whereIn('lesson_sort', $lessons->pluck('sort'));
            })
            ->with([
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

        if ($request->export) {
            return Excel::download(new BiuniqueAppointmentStarsExport($data), Carbon::now()->format('Y-m-d H:i:s') . '一对一学员星星明细表.xlsx');
        }

        return responseSuccess($data, 'Success.', [
            'courseMap' => $data->pluck('biuniqueCourse.title', 'biuniqueCourse.id'),
            'teacherMap' => $data->pluck('teacher_name', 'teacher_id'),
        ]);
    }
}
