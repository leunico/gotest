<?php

namespace Modules\ExaminationPaper\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class MajorProblemRource extends Resource
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
            'total_score' => $this->total_score,
            'examination_id' => $this->examination_id,
            'is_question_disorder' => $this->is_question_disorder,
            'is_option_disorder' => $this->is_option_disorder,
            'sort' => $this->sort,
            'description' => $this->description,
            'questions' => QuestionRource::collection($this->whenLoaded('questions')),
        ];
    }
}
