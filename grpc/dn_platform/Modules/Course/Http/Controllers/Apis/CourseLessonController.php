<?php

declare(strict_types=1);

namespace Modules\Course\Http\Controllers\Apis;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Course\Entities\CourseLesson;
use Modules\Course\Entities\CourseSection;
use Modules\Course\Transformers\CourseLessonResource;
use Illuminate\Http\JsonResponse;

class CourseLessonController extends Controller
{
    /**
     * 上课页面请求课程主题
     *
     * @param \Modules\Course\Entities\CourseLesson $lesson
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function show(CourseLesson $lesson): JsonResponse
    {
        $user = request()->user();
        $lesson->load([
            'sections' => function ($query) {
                $query->where('status', CourseSection::SECTION_STATUS_ON)
                    ->orderBy('section_number');
            },
            // 'sections.arduinoMaterial', // todo 需求又又又改了，不要这个了。。。
            'sections.learnProgresses' => function ($query) use ($user) {
                $query->select('id', 'section_id', 'collect_learn_record_id')
                    ->where('user_id', $user->id);
            },
            'sections.learnRecords' => function ($query) use ($user) {
                $query->select('id', 'section_id', 'end_at', 'duration')
                    ->where('user_id', $user->id);
            },
            'sections.problems' => function ($query) {
                $query->select('problems.*', 'course_section_problem_pivot.quize_time')
                    ->orderBy('course_section_problem_pivot.quize_time');
            },
            'sections.problems.subjectSubmission' => function ($query) use ($user) {
                $query->select('subject_submissions.id', 'problem_id', 'section_id')
                    ->where('user_id', $user->id);
            },
            'sections.problems.preview',
            'sections.problems.detail',
            'sections.problems.options' => function ($query) {
                $query->orderBy('sort');
            },
        ]);

        return $this->response()->item($lesson, CourseLessonResource::class);
    }
}
