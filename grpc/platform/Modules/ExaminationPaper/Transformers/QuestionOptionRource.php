<?php

namespace Modules\ExaminationPaper\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\PotentiallyMissing;

class QuestionOptionRource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        if (! ($this->whenLoaded('esort') instanceof PotentiallyMissing) && $this->esort) {
            $this->sort = $this->esort->sort;
        }

        return [
            'id' => $this->id,
            'option_title' => $this->option_title,
            // 'is_true' => $this->is_true, // 要吗？
            'question_id' => $this->question_id,
            'sort' => $this->sort,
        ];
    }
}
