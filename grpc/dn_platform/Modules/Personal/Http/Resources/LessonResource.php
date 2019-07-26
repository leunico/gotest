<?php

namespace Modules\Personal\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\Transform;
use Modules\Course\Transformers\CourseResource;

class LessonResource extends JsonResource
{
    use Transform;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'title' => (string) $this->title,
            'course_id' => (int) $this->course_id,
            'course' => (object) $this->transformItem($this->whenLoaded('course'), CourseResource::class),
            'cover_id' => (string) $this->cover_id,
            'materials' => (string) $this->materials,
            'tutorial_link' => (string) $this->tutorial_link,
            'lesson_intro' => (string) $this->lesson_intro,
            'knowledge' => (string) $this->knowledge,
            'sort' => (int) $this->sort,
            'count_user_learns' => (int) $this->sectcount_user_learnsion_intro,
            'status' => (int) $this->status,
            'is_code' => (int) $this->is_code,
            'sections' => (object) $this->transformCollection($this->whenLoaded('sections'), SectionResource::class),
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];
    }
}
