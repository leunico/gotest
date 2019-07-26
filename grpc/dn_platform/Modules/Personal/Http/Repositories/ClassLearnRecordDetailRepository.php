<?php

declare(strict_types=1);

namespace Modules\Personal\Http\Repositories;

use App\Http\Repositories\BaseRepository;
use Illuminate\Support\Carbon;
use Modules\Personal\Entities\LearnRecord;

class ClassLearnRecordDetailRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = $this->model()
            ->leftJoin('course_sections', 'course_sections.id', '=', 'learn_records.section_id')
            ->leftJoin('course_lessons', 'course_lessons.id', '=', 'course_sections.course_lesson_id')
            ->leftJoin('courses', 'courses.id', '=', 'course_lessons.course_id')
            ->whereNull('course_sections.deleted_at')
            ->whereNull('course_lessons.deleted_at')
            ->whereNull('courses.deleted_at')
            ->where('course_sections.status', '=', 1)
            ->where('course_lessons.status', '=', 1)
            ->where('courses.status', '=', 1);
    }

    /**
     * @return \Modules\Personal\Entities\LearnRecord
     */
    public function model()
    {
        return new LearnRecord();
    }

    /**
     * 最近学习时间
     *
     * @param string|null $startDate 2018-01-01
     * @param string|null $endDate
     * @return \Modules\Personal\Http\Controllers\Apis\ClassLearnRecordDetailRepository
     */
    public function date(?string $startDate, ?string $endDate): ClassLearnRecordDetailRepository
    {
        if ($startDate !== null) {
            $this->model->where('learn_records.entry_at', '>=', Carbon::parse($startDate)->startOfDay());
        }

        if ($endDate !== null) {
            $this->model->where('learn_records.entry_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        return $this;
    }

    /**
     * 课程分类
     *
     * @param integer|null $category
     * @return \Modules\Personal\Http\Controllers\Apis\ClassLearnRecordDetailRepository
     */
    public function courseCategory(?int $category): ClassLearnRecordDetailRepository
    {
        if ($category !== null) {
            $this->model->where('courses.category', '=', $category);
        }

        return $this;
    }
}
