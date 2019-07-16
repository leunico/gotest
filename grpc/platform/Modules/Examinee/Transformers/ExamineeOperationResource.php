<?php

namespace Modules\Examinee\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class ExamineeOperationResource extends Resource
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
            'examination_examinee_id' => $this->examination_examinee_id,
            'category' => $this->category,
            'remark' => $this->remark,
            'created_at' => $this->created_at
        ];
    }
}
