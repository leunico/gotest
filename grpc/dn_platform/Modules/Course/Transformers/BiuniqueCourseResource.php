<?php

namespace Modules\Course\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Modules\Course\Entities\BiuniqueCourse;

class BiuniqueCourseResource extends Resource
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
            'category' => $this->category,
            'categoryStr' => isset(BiuniqueCourse::$categoryMap[$this->category]) ? BiuniqueCourse::$categoryMap[$this->category] : '-',
            'price_star' => $this->price_star,
            'status' => $this->status,
            'is_audition' => $this->is_audition,
            'sort' => $this->sort,
            'lessons' => BiuniqueCourseLessonResource::collection($this->whenLoaded('biuniqueLessons')),
            'newAppointment' => $this->whenLoaded('newAppointment')
        ];
    }
}
