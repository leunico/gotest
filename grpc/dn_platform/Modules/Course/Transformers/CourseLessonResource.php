<?php

namespace Modules\Course\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Modules\Course\Entities\CourseLesson;
use Modules\Course\Entities\CourseSection;
use Carbon\Carbon;

class CourseLessonResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $lesson = collect([]);
        if ($request->routeIs('course-lesson-show')) {
            $lesson = CourseLesson::select('id', 'course_id', 'is_code', 'sort', 'is_drainage')
                ->whereHas('sections', function ($query) {
                    $query->where('status', CourseSection::SECTION_STATUS_ON);
                })
                ->where('course_id', $this->course_id)
                ->whereIn('sort', [$this->sort + 1, $this->sort - 1])
                ->get()
                ->keyBy('sort');
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'materials' => $this->materials,
            'tutorial_link' => $this->tutorial_link,
            'work' => $this->work,
            'lesson_intro' => $this->lesson_intro,
            'count_user_learns' => $this->count_user_learns,
            'knowledge' => $this->knowledge,
            'sort' => $this->sort,
            'prev' => $this->when($lesson->get($this->sort - 1), function () use ($lesson, $request) {
                $lesson = $lesson->get($this->sort - 1);
                return ['id' => $lesson->id, 'is_code' => $lesson->is_code, 'is_unlock' => $request->user()->can('show', $lesson)];
            }, null),
            'next' => $this->when($lesson->get($this->sort + 1), function () use ($lesson, $request) {
                $lesson = $lesson->get($this->sort + 1);
                return ['id' => $lesson->id, 'is_code' => $lesson->is_code, 'is_unlock' => $request->user()->can('show', $lesson)];
            }, null),
            'sections' => CourseSectionResource::collection($this->sections)
        ];
    }
}
