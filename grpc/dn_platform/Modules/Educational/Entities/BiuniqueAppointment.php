<?php

namespace Modules\Educational\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Course\Entities\BiuniqueCourse;
use App\User;
use Modules\Course\Entities\BiuniqueCourseLesson;
use App\File;

class BiuniqueAppointment extends Model
{
    use SoftDeletes;

    const STATUS_OVER = 2;
    const STATUS_NO = 1;
    const STATUS_OFF = 0;
    const ATTENDANCE_OFF = 0;
    const ATTENDANCE_OK = 1;
    const ATTENDANCE_LOSE = 2;

    public static $attendanceMap = [
        self::ATTENDANCE_OFF => '未知',
        self::ATTENDANCE_OK => '正常',
        self::ATTENDANCE_LOSE => '缺勤'
    ];

    protected $fillable = [
        'teacher_office_time_id'
    ];

    public function biuniqueCourse()
    {
        return $this->belongsTo(BiuniqueCourse::class);
    }

    public function teacherOfficeTime()
    {
        return $this->belongsTo(TeacherOfficeTime::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id')
            ->select('id', 'name', 'real_name');
    }

    public function files()
    {
        return $this->belongsToMany(File::class, 'biunique_appointment_files')
            ->select('files.id', 'files.origin_filename', 'files.driver_baseurl', 'files.filename', 'files.mime')
            ->withPivot('resource_name');
    }

    /**
     * 获取最新的课时
     *
     * @param integer $user_id
     * @return integer
     */
    public function lastLessonSort(int $user_id)
    {
        $last = self::where('user_id', $user_id)
            ->where('biunique_course_id', $this->biunique_course_id)
            ->orderBy('lesson_sort', 'desc')
            ->first();

        return $last ? ($last->lesson_sort + 1) : 1;
    }
}
