<?php

declare(strict_types=1);

namespace Modules\Personal\Http\Repositories;

use App\User;
use App\Http\Repositories\BaseRepository;
use Illuminate\Support\Carbon;
use Modules\Operate\Entities\Order;
use Modules\Educational\Entities\StudyClass;
use Modules\Course\Entities\Course;
use Modules\Course\Entities\CourseLesson;

class CourseLessonLearnRecordRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = $this->model()
            ->leftJoin('courses', 'courses.id', '=', 'course_lessons.course_id')
            ->whereNull('courses.deleted_at')
            ->whereNull('course_lessons.deleted_at');
    }

    /**
     * @return \Modules\Course\Entities\Course
     */
    public function model()
    {
        return new CourseLesson();
    }

    /**
     * 课程类型筛选
     *
     * @param integer $courseId
     * @return \Modules\Personal\Http\Controllers\Apis\CourseLessonLearnRecordRepository
     */
    public function category(int $category): CourseLessonLearnRecordRepository
    {
        if ($category) {
            $this->model->where('courses.category', '=', $category);
        }

        return $this;
    }

    /**
     * 主题筛选
     *
     * @param integer $lessonId
     * @return \Modules\Personal\Http\Controllers\Apis\CourseLessonLearnRecordRepository
     */
    public function lesson(int $lessonId): CourseLessonLearnRecordRepository
    {
        if ($lessonId) {
            $this->model->where('course_lessons.id', '=', $lessonId);
        }

        return $this;
    }

    /**
     * 关键词搜索
     *
     * @param string|null $keyword
     * @return \Modules\Personal\Http\Controllers\Apis\CourseLessonLearnRecordRepository
     */
    public function keyword(?string $keyword): CourseLessonLearnRecordRepository
    {
        if ($keyword !== null) {
            $this->model->where('courses.title', 'like', "{$keyword}%");
        }

        return $this;
    }
}
