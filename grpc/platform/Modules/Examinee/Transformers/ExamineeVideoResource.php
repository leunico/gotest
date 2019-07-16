<?php

namespace Modules\Examinee\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class ExamineeVideoResource extends Resource
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
            'video_url' => $this->video_url,
            'type' => $this->type,
            'created_at' => $this->created_at
        ];
    }
}
