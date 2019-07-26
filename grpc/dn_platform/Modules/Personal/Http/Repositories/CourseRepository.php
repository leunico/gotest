<?php

declare(strict_types=1);

namespace Modules\Personal\Http\Repositories;

use App\Http\Repositories\BaseRepository;
use Modules\Course\Entities\Course;

class CourseRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = $this->model()
            ->leftJoin('course_users', 'course_users.course_id', '=', 'courses.id')
            ->whereNull('courses.deleted_at')
            ->whereNull('course_users.deleted_at');
    }

    /**
     * @return \Modules\Course\Entities\Course
     */
    public function model()
    {
        return new Course();
    }

    /**
     * 课程筛选
     *
     * @param string $key
     * @param integer|null $value
     * @return \Modules\Personal\Http\Controllers\Apis\CourseRepository
     */
    public function course(string $key, ?int $value): CourseRepository
    {
        if ($value !== null) {
            $this->model->where("courses.{$key}", $value);
        }

        return $this;
    }

    /**
     * 关键词搜索
     *
     * @param string|null $keyword
     * @return \Modules\Personal\Http\Controllers\Apis\CourseRepository
     */
    public function keyword(?string $keyword): CourseRepository
    {
        if ($keyword !== null) {
            $this->model->where('courses.title', 'like', "{$keyword}%");
        }

        return $this;
    }
}
