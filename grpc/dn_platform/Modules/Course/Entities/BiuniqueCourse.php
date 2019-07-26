<?php

namespace Modules\Course\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Educational\Entities\BiuniqueAppointment;

class BiuniqueCourse extends Model
{
    use SoftDeletes;

    const STATUS_ON = 1;
    const STATUS_OFF = 0;
    const CATEGORY_YY = 1;
    const CATEGORY_YH = 2;
    const CATEGORY_SY = 3;
    const CATEGORY_GQ = 4;
    const IS_AUDITION_ON = 1;
    const IS_AUDITION_OFF = 0;

    protected $fillable = [];

    public static $categoryMap = [
        self::CATEGORY_YY => '央音音基考试',
        self::CATEGORY_YH => '英皇乐理考级',
        self::CATEGORY_SY => '声乐练习',
        self::CATEGORY_GQ => '钢琴陪练',
    ];

    public function isNotAudition()
    {
        return $this->is_audition == self::IS_AUDITION_OFF;
    }

    public function actionStatus()
    {
        return empty($this->status) ? $this->increment('status') : $this->decrement('status');
    }

    public function biuniqueLessons()
    {
        return $this->hasMany(BiuniqueCourseLesson::class);
    }

    public function biuniqueResources()
    {
        return $this->hasMany(BiuniqueCourseResource::class);
    }

    public function appointments()
    {
        return $this->hasMany(BiuniqueAppointment::class);
    }

    public function newAppointment()
    {
        return $this->hasOne(BiuniqueAppointment::class)
            ->orderBy('lesson_sort', 'desc');
    }

    public function lastCourseSort()
    {
        $last = self::select('id', 'sort')
            ->orderBy('sort', 'desc')
            ->first();

        return $last ? (int) $last->sort : 1;
    }
}
