<?php

namespace Modules\Course\Http\Controllers\Apis;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Course\Entities\Course;
use Modules\Course\Entities\BigCourse;
use Modules\Course\Transformers\BigCourseResource;

class BigCourseController extends Controller
{
    /**
     * Show the big course of wechat.
     *
     * @param \Modules\Course\Entities\BigCourse $course
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function wechatShow(BigCourse $course): JsonResponse
    {
        $course->load([
            'cover',
            'courses' => function ($query) {
                $query->select('courses.id', 'courses.title', 'courses.course_intro', 'price', 'courses.cover_id', 'level', 'courses.category', 'is_mail')
                    ->where('status', Course::STATUS_NO);
            },
            'courses.cover'
        ]);

        return $this->response()->item($course, BigCourseResource::class);
    }
}
