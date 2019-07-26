<?php

namespace Modules\Course\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class ProblemDetailResource extends Resource
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
            'problem_text' => $this->problem_text,
            'answer' => $this->answer,
            'hint' => $this->hint,
        ];
    }
}
