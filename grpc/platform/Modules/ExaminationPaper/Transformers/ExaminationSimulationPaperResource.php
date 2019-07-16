<?php

namespace Modules\ExaminationPaper\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class ExaminationSimulationPaperResource extends Resource
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
            'examination_category_id' => $this->examination_category_id,
            'content' => $this->content,
            'type_question' => $this->when($request->type, function () use ($request) {
                return empty($this->content) ? $this->content : collect($this->content['major_problems'])
                    ->pluck('questions')
                    ->collapse()
                    ->where('category', $request->type)
                    ->all();
            })
        ];
    }
}
