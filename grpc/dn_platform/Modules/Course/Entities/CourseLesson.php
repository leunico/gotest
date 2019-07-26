<?php

namespace Modules\Course\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\File;
use Modules\Personal\Entities\Work;
use Modules\Personal\Entities\CollectLearnRecord;
use Modules\Educational\Entities\ClassCourseLessonUnlocak;

class CourseLesson extends Model
{
    use SoftDeletes;

    const LESSON_STATUS_ON = 1;

    const LESSON_STATUS_OFF = 0;

    const LESSON_OFF_SORT = 1000; // todo 不要随便改这个

    protected $fillable = [];

    /**
     * 获取最后一个排序
     *
     * @param int $course_id
     * @return integer
     */
    public function lastSort(int $course_id)
    {
        $last = $this->where('course_id', $course_id)
            ->where('status', self::LESSON_STATUS_ON)
            ->where('sort', '<', self::LESSON_OFF_SORT)
            ->orderBy('sort', 'desc')
            ->select('sort')
            ->first();

        return empty($last) ? 0 : ((int) $last->sort + 1);
    }

    public function isNotDrainage()
    {
        return ! empty($this->is_drainage);
    }

    public function scopeOfCourseId($query, $id)
    {
        return empty($id) ? $query : $query->where('course_id', $id);
    }

    public function cover()
    {
        return $this->belongsTo(File::class, 'cover_id')
            ->select('driver_baseurl', 'origin_filename', 'id', 'filename');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function sections()
    {
        return $this->hasMany(CourseSection::class)
            ->orderBy('section_number');
    }

    public function works()
    {
        return $this->hasMany(Work::class, 'lesson_id');
    }

    public function learnRecords()
    {
        return $this->hasMany(CollectLearnRecord::class);
    }

    public function userLearnRecord()
    {
        return $this->hasOne(CollectLearnRecord::class);
    }

    public function unlockDays()
    {
        return $this->hasOne(ClassCourseLessonUnlocak::class);
    }
}
