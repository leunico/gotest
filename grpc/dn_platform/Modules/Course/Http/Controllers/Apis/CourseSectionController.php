<?php

declare(strict_types=1);

namespace Modules\Course\Http\Controllers\Apis;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Course\Entities\CourseSection;
use App\Http\Controllers\Controller;
use \Illuminate\Http\JsonResponse;
use Modules\Course\Transformers\ProblemResource;

class CourseSectionController extends Controller
{
    /**
     * 环节请求题目列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\CourseSection $section
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function problems(Request $request, CourseSection $section): JsonResponse
    {
        $user = $request->user();
        $quizTime = $request->input('quize_time', null);
        $section->load([
            'problems' => function ($query) use ($quizTime) {
                $query->when($quizTime, function ($query) use ($quizTime) {
                    $query->where('course_section_problem_pivot.quize_time', $quizTime);
                })->select('problems.*', 'course_section_problem_pivot.quize_time');
            },
            'problems.subjectSubmission' => function ($query) use ($user) {
                $query->select('subject_submissions.id', 'problem_id', 'section_id')
                    ->where('user_id', $user->id);
            },
            'problems.preview',
            'problems.detail',
            'problems.options',
        ]);

        return $this->response()->collection($section->problems, ProblemResource::class);
    }
}
