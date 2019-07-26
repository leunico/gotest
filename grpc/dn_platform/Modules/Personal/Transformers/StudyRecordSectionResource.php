<?php

namespace Modules\Personal\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class StudyRecordSectionResource extends Resource
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
            'title' => $this->title,
            'category' => $this->category,
            'source_duration' => $this->source_duration,
            'learn_time' => $this->learn_time,
            'section_subjects' => StudyRecordSubjectResource::collection($this->sectionPivots),
        ];
    }
}
