<?php

namespace Modules\Educational\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\User;

class TeacherOfficeTime extends Model
{
    const STATUS_OFF = 0;
    const STATUS_ON = 1;
    const TYPE_ZS = 1;
    const TYPE_ST = 2;

    const DEFAULT_TIMES = [
        ["17:00-17:40", "18:00-18:40", "19:00-19:40", "20:00-20:40"],
        ["17:00-17:40", "18:00-18:40", "19:00-19:40", "20:00-20:40"],
        ["17:00-17:40", "18:00-18:40", "19:00-19:40", "20:00-20:40"],
        ["17:00-17:40", "18:00-18:40", "19:00-19:40", "20:00-20:40"],
        ["17:00-17:40", "18:00-18:40", "19:00-19:40", "20:00-20:40"],
        ["09:00-09:40", "10:00-10:40", "11:00-11:40", "14:00-14:40", "15:00-15:40", "16:00-16:40", "17:00-17:40", "18:00-18:40", "19:00-19:40", "20:00-20:40"],
        ["09:00-09:40", "10:00-10:40", "11:00-11:40", "14:00-14:40", "15:00-15:40", "16:00-16:40", "17:00-17:40", "18:00-18:40", "19:00-19:40", "20:00-20:40"]
    ];

    const ALL_TIMES = [
        "09:00-09:40",
        "10:00-10:40",
        "11:00-11:40",
        "14:00-14:40",
        "15:00-15:40",
        "16:00-16:40",
        "17:00-17:40",
        "18:00-18:40",
        "19:00-19:40",
        "20:00-20:40"
    ];

    public static $typeMap = [
        self::TYPE_ZS => '正式课',
        self::TYPE_ST => '试听课'
    ];

    protected $fillable = [
        'user_id',
        'appointment_date',
        'times',
        'status'
    ];

    protected $casts = [
        'times' => 'json'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function biuniqueAppointment()
    {
        return $this->hasOne(BiuniqueAppointment::class);
    }

    public function isAudition()
    {
        return $this->type == self::TYPE_ST;
    }

    public function isFormal()
    {
        return $this->type == self::TYPE_ZS;
    }

    /**
     * 添加默认设置时间
     *
     * @param integer $user_id
     * @param integer $type
     * @param string $startTime
     * @return void
     */
    public function setDefaultTimes(int $user_id, int $type, string $startTime): Collection
    {
        $data = [];
        $date = Carbon::parse($startTime)->startOfWeek()->subDay();
        foreach (self::DEFAULT_TIMES as $value) {
            $val['created_at'] = Carbon::now();
            $val['updated_at'] = Carbon::now();
            $val['user_id'] = $user_id;
            $val['type'] = $type;
            $strDate = $date->addDay()->toDateString();
            foreach ($value as $item) {
                $val['appointment_date'] = $strDate .' '. str_before($item, '-');
                $val['end_date'] = $strDate .' '. str_after($item, '-');
                $val['time'] = $item;
                $data[] = $val;
            }
        }

        return self::insert($data) ? self::where('user_id', $user_id)
            ->whereBetween('appointment_date', [Carbon::parse($startTime)->startOfWeek(), $date->endOfDay()])
            ->get() : collect();
    }

    /**
     * 获取排最前的老师或者指定老师
     *
     * @param string $appointmentDate
     * @param integer $course
     * @param integer $type
     * @param integer|null $teacher
     * @return self|null
     */
    public function getRankTeacher(string $appointmentDate, int $course, ?int $teacher = null, int $type = TeacherCourse::TYPE_ZS): ?self
    {
        $teacherOffices = self::where('appointment_date', $appointmentDate)
            ->select(
                'teacher_office_times.id',
                'teacher_office_times.sort',
                'teacher_courses.sort as default_sort',
                'teacher_office_times.user_id',
                'teacher_office_times.type',
                'appointment_date'
            )
            ->join('teacher_courses', 'teacher_office_times.user_id', 'teacher_courses.user_id')
            ->when($teacher, function ($query) use ($teacher) {
                return $query->where('teacher_courses.user_id', $teacher);
            })
            ->where('teacher_courses.type', $type)
            ->where('status', self::STATUS_OFF)
            ->where('biunique_course_id', $course)
            ->whereColumn('teacher_office_times.type', 'teacher_courses.type')
            ->orderBy('sort', 'desc')
            ->orderBy('default_sort', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return $teacherOffices->isEmpty() ? null : $teacherOffices->first();
    }
}
