<?php

declare(strict_types=1);

namespace Modules\Course\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Personal\Http\Resources\CourseResource;
use Modules\Course\Entities\Course;
use Modules\Course\Entities\CourseLesson;
use Modules\Course\Entities\Tag;
use Illuminate\Http\Request;
use Modules\Course\Transformers\TagResource;
use Modules\Personal\Entities\CourseUser;
use App\User;
use Modules\Course\Entities\BiuniqueCourse;
use Modules\Course\Transformers\BiuniqueCourseResource;

/**
 * 工具类，提供给其他模块的前端使用
 */
class ToolController extends Controller
{
    /**
     * 课程列表
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function course(Request $request): JsonResponse
    {
        $query = Course::where('courses.status', Course::STATUS_NO)
            ->with([
                'lessons' => function ($query) {
                    $query->where('status', CourseLesson::LESSON_STATUS_ON);
                },
            ]);

        if ($request->has('user_id')) {
            $query->leftJoin('course_users', 'course_users.course_id', '=', 'courses.id')
                ->where('course_users.user_id', '=', (int) $request->user_id)
                ->where('course_users.status', '=', CourseUser::STATUS_NO);
        }

        if ($request->has('category')) {
            $query->where('courses.category', (int) $request->category);
        }

        $courses = $query->select(['courses.*'])->get();

        return $this->response()->collection($courses, CourseResource::class);
    }

    /**
     * 一对一课程列表
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function biuniqueCourse(Request $request): JsonResponse
    {
        $category = $request->input('category', null);
        $isAudition = $request->input('is_audition', null);
        $status = $request->input('status', 1);

        $courses = BiuniqueCourse::select('id', 'title', 'category', 'sort', 'price_star', 'is_audition')
            ->when(in_array($category, array_keys(BiuniqueCourse::$categoryMap)), function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->when(! is_null($isAudition), function ($query) use ($isAudition) {
                $query->where('is_audition', $isAudition);
            })
            ->when(! is_null($status), function ($query) use ($status) {
                $query->where('status', $status);
            })
            // ->where('status', BiuniqueCourse::STATUS_ON)
            ->with([
                'newAppointment' => function ($query) {
                    $query->select('id', 'teacher_office_time_id', 'lesson_sort', 'biunique_course_id')
                        ->where('user_id', $this->user() ? $this->user()->id : null);
                }
            ])
            ->orderBy('sort')
            ->get();

        return $this->response()->collection($courses, BiuniqueCourseResource::class);
    }

    /**
     * 标签列表
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function tags(Request $request): JsonResponse
    {
        $category = $request->input('category', null);

        $courses = Tag::select('id', 'name', 'category', 'sort')
            ->when(in_array($category, Tag::CATEGORYS), function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->orderBy('sort')
            ->get();

        return $this->response()->collection($courses, TagResource::class);
    }
}
