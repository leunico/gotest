<?php

namespace Modules\Course\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class BiuniqueCourseLessonResource extends Resource
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
            'introduce' => $this->introduce,
            'biunique_course_id' => $this->biunique_course_id,
            'status' => $this->status,
            'sort' => $this->sort
        ];
    }
}
