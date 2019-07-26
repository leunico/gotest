<?php

namespace Modules\Course\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BiuniqueCourseLesson extends Model
{
    use SoftDeletes;

    const LESSON_STATUS_ON = 1;
    const LESSON_STATUS_OFF = 0;
    const LESSON_OFF_SORT = 1000;
    const LESSON_START_SORT = 1;

    protected $fillable = [];

    /**
     * 获取最后一个排序
     *
     * @param int $course_id
     * @return integer
     */
    public function lastSort(int $course_id)
    {
        $last = $this->where('biunique_course_id', $course_id)
            ->where('status', self::LESSON_STATUS_ON)
            ->where('sort', '<', self::LESSON_OFF_SORT)
            ->orderBy('sort', 'desc')
            ->select('sort')
            ->first();

        return empty($last) ? self::LESSON_START_SORT : ((int) $last->sort + 1);
    }

    public function scopeOfBiuniqueCourseId($query, $id)
    {
        return empty($id) ? $query : $query->where('biunique_course_id', $id);
    }

    public function resources()
    {
        return $this->belongsToMany(BiuniqueCourseResource::class, 'biunique_course_lesson_resources');
    }
}
