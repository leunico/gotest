<?php

namespace Modules\Personal\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class StudyRecordSubjectResource extends Resource
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
            'problem_id' => $this->problem_id,
            'correct_number' => $this->correct_number,
            'error_number' => $this->error_number,
            'subject_total_number' => $this->subject_total_number,
            'problem_title' => $this->detail->problem_text,
            'category' => $this->problem->category,
        ];
    }
}
