<?php

namespace Modules\Examinee\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\FileResource;

class ExamineeTencentFaceResource extends Resource
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
            'request_id' => $this->request_id,
            'description' => $this->description,
            'result' => $this->result,
            'sim' => $this->sim,
            'examination_examinee_id' => $this->examination_examinee_id,
            'best_file' => new FileResource($this->whenLoaded('bestFile')),
            'category' => $this->category,
            'type' => $this->type,
            'created_at' => $this->created_at
        ];
    }
}
