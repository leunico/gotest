<?php

namespace Modules\Examination\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\FileResource;
use Modules\ExaminationPaper\Transformers\MajorProblemRource;

class ExaminationRource extends Resource
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
            'is_hand' => $this->is_hand,
            'examination_paper_title' => $this->examination_paper_title,
            'examination_examinee_id' => $this->examination_examinee_id,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'age_min' => $this->age_min,
            'age_max' => $this->age_max,
            'description' => $this->description,
            'testing_status' => $this->testing_status,
            'category' => $this->category,
            'exam_file' => new FileResource($this->whenLoaded('examFile')),
            'major_problems' => MajorProblemRource::collection($this->whenLoaded('majorProblems')),
        ];
    }
}
