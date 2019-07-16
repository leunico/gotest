<?php

namespace Modules\Examinee\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Modules\ExaminationPaper\Entities\Question;

class ExamineeAnswerResource extends Resource
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
            'examinee_id' => $this->examinee_id,
            'examination_id' => $this->examination_id,
            'question_id' => $this->question_id,
            'question_option_id' => $this->question_option_id,
            'answer' => $this->answer,
            'answer_file' => $this->answer_file,
            'answer_time' => $this->answer_time,
            'type' => $this->type,
            'type_str' => Question::$categorys[$this->type] ?? '',
        ];
    }
}
