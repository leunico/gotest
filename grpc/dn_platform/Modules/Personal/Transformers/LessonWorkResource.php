<?php

namespace Modules\Personal\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class lessonWorkResource extends Resource
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
            'cover' => $this->cover,
            'work_count' => count($this->works),
        ];
    }
}
