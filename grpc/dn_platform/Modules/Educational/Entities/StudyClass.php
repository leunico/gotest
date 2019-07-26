<?php

declare(strict_types=1);

namespace Modules\Educational\Entities;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Modules\Course\Entities\Course;
use Modules\Course\Entities\BigCourse;
use Modules\Course\Entities\CourseLesson;
use Illuminate\Support\Carbon;
use Modules\Course\Entities\BigCourseCoursePivot;

class StudyClass extends Model
{
    const UNLOCK_DAY_COUNT = 60;
    const CATEGORY_COURSE = 2;
    const CATEGORY_BIG_COURSE = 1;
    const PATTERN_GD = 1;
    const FREQUENCY_WEEK = 1;
    const FREQUENCY_MONTH = 2;
    const STATUS_ON = 1;
    const STATUS_OFF = 0;

    public static $categorys = [
        self::CATEGORY_BIG_COURSE => '年微课【大课程】',
        self::CATEGORY_COURSE => '系列课'
    ];

    public static $patterns = [
        self::PATTERN_GD => '固定时间'
    ];

    public static $frequencies = [
        self::FREQUENCY_WEEK => '每周',
        self::FREQUENCY_MONTH => '每月'
    ];

    protected $table = 'classes';

    protected $fillable = [];

    protected $casts = [
        'unlocak_times' => 'json'
    ];

    public function isFrequencyWeek()
    {
        return $this->frequency == self::FREQUENCY_WEEK;
    }

    public function isFrequencyMonth()
    {
        return $this->frequency == self::FREQUENCY_MONTH;
    }

    public function isCategoryCourse()
    {
        return $this->category == self::CATEGORY_COURSE;
    }

    public function isCategoryBigCourse()
    {
        return $this->category == self::CATEGORY_BIG_COURSE;
    }

    public function actionStatus()
    {
        return empty($this->status) ? $this->increment('status') : $this->decrement('status');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id')
            ->select('id', 'name', 'real_name', 'sex', 'phone');
    }

    public function coursePovits()
    {
        return $this->hasMany(ClassCourse::class, 'class_id')
            ->select('id', 'course_id');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'class_courses', 'class_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function bigCourse()
    {
        return $this->belongsTo(BigCourse::class, 'big_course_id');
    }

    public function bigCoursePivots()
    {
        return $this->hasMany(BigCourseCoursePivot::class, 'big_course_id', 'big_course_id')
            ->select('id', 'course_id');
    }

    public function classStudents()
    {
        return $this->hasMany(ClassStudent::class, 'class_id')
            ->select('id', 'user_id', 'class_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'class_students', 'class_id')
            ->withPivot('id')
            ->withTimestamps();
    }

    public function courseLessons()
    {
        return $this->belongsToMany(CourseLesson::class, 'class_course_lesson_unlocaks', 'class_id')
            ->select('course_lessons.id', 'title', 'course_lessons.course_id', 'class_course_lesson_unlocaks.unlock_day');
    }

    public function classCourseLesson()
    {
        return $this->hasOne(ClassCourseLessonUnlocak::class, 'class_id')
            ->select('id', 'course_id', 'class_id', 'course_lesson_id', 'unlock_day');
    }

    public function classCourseLessons()
    {
        return $this->hasMany(ClassCourseLessonUnlocak::class, 'class_id')
            ->select('id', 'course_id', 'class_id', 'course_lesson_id', 'unlock_day');
    }

    /**
     * 获取解锁时间列表。
     *
     * @return array
     */
    public function getUnlockListsAttribute(): array
    {
        $unlockList = [];
        if ($this->pattern != self::PATTERN_GD) {
            return $unlockList;
        }

        $holidays = Holiday::all()->keyBy('date');
        $startAt = $this->unlock_at ?? $this->leave_at;
        for ($i=0; $i < self::UNLOCK_DAY_COUNT; $i++) {
            foreach ($this->unlocak_times as $key => $value) {
                $value = explode(':', $value);
                if ($this->isFrequencyWeek()) {
                    $unlockList[$i][$key] = isset($unlockList[$i-1][$key]) ?
                        Carbon::parse(str_before($unlockList[$i-1][$key], '['))->addWeeks(1)->toDateTimeString() :
                        Carbon::parse($startAt)->next($key == 7 ? 0 : $key)->hour($value[0])->minute($value[1])->toDateTimeString();
                } elseif ($this->isFrequencyMonth()) {
                    $unlockList[$i][$key] = isset($unlockList[$i-1][$key]) ?
                        Carbon::parse(str_before($unlockList[$i-1][$key], '['))->addMonths(1)->toDateTimeString() :
                        Carbon::parse($startAt)->day($key)->hour($value[0])->minute($value[1])->toDateTimeString();
                }

                if ($holiday = $holidays->get(Carbon::parse($unlockList[$i][$key])->startOfDay()->toDateTimeString())) {
                    $unlockList[$i][$key] = $unlockList[$i][$key] . '[' . $holiday->name . ']';
                }
            }
        }

        $unlockList = array_flatten($unlockList);
        sort($unlockList);

        return $unlockList;
    }
}
