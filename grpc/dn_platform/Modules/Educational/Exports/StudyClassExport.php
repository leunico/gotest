<?php

namespace Modules\Educational\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Modules\Personal\Entities\CourseUser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StudyClassExport implements FromCollection
{
    /**
     * \Illuminate\Http\Request
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $keyword = $this->request->input('keyword', null);

        $values = [['用户名', '姓名', '手机号', '性别', '年级', '状态', '班级名称', '班级状态', '开课日期', '结课日期']];
        CourseUser::select('course_users.id', 'user_id', 'course_id')
            ->where('course_users.status', CourseUser::STATUS_NO)
            ->when($keyword, function ($query) use ($keyword) {
                $query->whereIn('course_users.user_id', function ($query) use ($keyword) {
                    $query->from('users')
                        ->select('id')
                        ->where('real_name', 'LIKE', "%$keyword%")
                        ->orWhere('users.name', 'LIKE', "%$keyword%")
                        ->orWhere('phone', 'LIKE', "%$keyword%");
                });
            })
            ->with([
                'user' => function ($query) {
                    return $query->select('name', 'real_name', 'users.id', 'phone', 'grade', 'sex');
                },
                'user.allClass' => function ($query) {
                    return $query->select('classes.id', 'classes.name', 'classes.status', 'entry_at', 'leave_at', 'classes.category', 'classes.course_id', 'big_course_id');
                },
                'user.allClass.bigCoursePivots' => function ($query) {
                    return $query->select('big_course_course_pivot.course_id', 'big_course_course_pivot.id', 'big_course_course_pivot.big_course_id');
                }
            ])
            ->get()
            ->map(function ($item) use (&$values) {
                if ($item->user) {
                    $val[0] = $item->user->name;
                    $val[1] = $item->user->real_name;
                    $val[2] = $item->user->phone;
                    $val[3] = isset(User::$sexMap[$item->user->sex]) ? User::$sexMap[$item->user->sex] : '-';
                    $val[4] = isset(User::$gradeMap[$item->user->grade]) ? User::$gradeMap[$item->user->grade] : '-';
                    $val[6] = $val[7] = $val[8] = $val[9] = '-';
                    if ($item->user->allClass->isNotEmpty()) {
                        $item->user->allClass->each(function ($v) use ($item, &$val) {
                            if ($v->course_id == $item->course_id || ($v->bigCoursePivots && $v->bigCoursePivots->where('course_id', $item->course_id)->isNotEmpty())) {
                                $val[6] = $v->name;
                                $val[7] = empty($v->status) ? '未发布' : '已发布';
                                $val[8] = $v->entry_at;
                                $val[9] = $v->leave_at;
                            }
                        });
                    }
                    if (empty($item['class_id'])) {
                        $val[5] = '待排班';
                    } elseif ($item['entry_at'] && Carbon::now()->lt(Carbon::parse($item['entry_at']))) {
                        $val[5] = '待开课';
                    } elseif ($item['leave_at'] && Carbon::now()->gt(Carbon::parse($item['leave_at']))) {
                        $val[5] = '已结课';
                    } elseif ($item['entry_at'] && Carbon::now()->gte(Carbon::parse($item['entry_at']) && Carbon::now()->lte(Carbon::parse($item['leave_at'])))) {
                        $val[5] = '上课中';
                    } else {
                        $val[5] = '-';
                    }
                    ksort($val);
                    $values[] = $val;
                }
            });

        return collect($values);
    }
}
