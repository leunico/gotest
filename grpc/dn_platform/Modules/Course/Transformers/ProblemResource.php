<?php

namespace Modules\Course\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\FileResource;

class ProblemResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'category' => $this->category,
            'course_category' => $this->course_category,
            'preview_id' => $this->preview_id,
            'use_count' => $this->use_count,
            'plan_duration' => $this->plan_duration,
            'course_section_id' => $this->pivot->course_section_id,
            'quize_time' => $this->quize_time,
            'preview' => new FileResource($this->preview),
            'detail' => new ProblemDetailResource($this->detail),
            'options' => ProblemOptionResource::collection($this->options),
            'has_submit' => $this->subjectSubmission->contains('section_id', $this->pivot->course_section_id),
        ];
    }
}
