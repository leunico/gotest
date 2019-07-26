<?php

namespace Modules\Personal\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\Transform;

class SectionResource extends JsonResource
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
        $data = [
            'id' => (int) $this->id,
            'title' => (string) $this->title,
            'course_lesson_id' => (int) $this->course_lesson_id,
            'course_lesson' => (object) $this->transformItem($this->whenLoaded('courseLesson'), LessonResource::class),
            'arduino_material_id' => (string) $this->arduino_material_id,
            'category' => (int) $this->category,
            'source_link' => (string) $this->source_link,
            'source_duration' => (int) $this->source_duration,
            'status' => (int) $this->status,
            'section_number' => (int) $this->section_number,
            'section_intro' => (string) $this->section_intro,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];

        if ($this->relationLoaded('learnProgresses')) {
            $data['learn_progresses'] = $this->transformCollection($this->learnProgresses, LearnProgressesResource::class);
        }

        if ($this->relationLoaded('learnRecords')) {
            $data['learn_records'] = $this->transformCollection($this->learnRecords, LearnRecordsResource::class);
        }

        return $data;
    }
}
