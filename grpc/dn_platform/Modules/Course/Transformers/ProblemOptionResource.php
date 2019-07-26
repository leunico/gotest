<?php

namespace Modules\Course\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class ProblemOptionResource extends Resource
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
            'option_text' => $this->option_text,
            'is_true' => $this->is_true,
            'sort' => $this->sort,
        ];
    }
}
