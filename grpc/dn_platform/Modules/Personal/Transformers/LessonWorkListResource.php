<?php

namespace Modules\Personal\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class LessonWorkListResource extends Resource
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
            'id' => $this->course->id,
            'title' => $this->course->title,
            'lessons' => LessonWorkResource::collection($this->lessons),
        ];
    }
}
