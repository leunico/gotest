<?php

namespace Modules\Personal\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\Transform;

class CourseResource extends JsonResource
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
            'course_intro' => (string) $this->course_intro,
            'level' => (int) $this->level,
            'category' => (int) $this->category,
            'status' => (int) $this->status,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];

        if ($this->relationLoaded('lessons')) {
            $data['lessons'] = $this->transformCollection($this->lessons, LessonResource::class);
        }

        if ($this->getAttributeValue('learn_records_total') !== null) {
            $data['learn_records_total'] = (int) $this->learn_records_total;
        }

        if ($this->getAttributeValue('learn_records_total_format') !== null) {
            $data['learn_records_total_format'] = (string) $this->learn_records_total_format;
        }

        if ($this->getAttributeValue('finish_section_percent') !== null) {
            $data['finish_section_percent'] = (string) $this->finish_section_percent;
        }

        return $data;
    }
}
