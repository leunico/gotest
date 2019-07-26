<?php

namespace Modules\Personal\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class StudyRecordResource extends Resource
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
            'lesson_intro' => $this->lesson_intro,
            'knowledge' => $this->knowledge,
            'sort' => $this->sort,
            'total_subject' => $this->total_subject,
            'total_correct_number' => $this->total_correct_number,
            'total_error_number' => $this->total_error_number,
            'total_submit_number' => $this->total_submit_number,
            'works' => StudyRecordWorkResource::collection($this->works),
            'sections' => StudyRecordSectionResource::collection($this->sections),
        ];
    }
}
