<?php

namespace Modules\Course\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\FileResource;

class BigCourseResource extends Resource
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
            'course_intro' => $this->when($this->course_intro, $this->course_intro),
            'category' => $this->when($this->category, $this->category),
            'cover' => new FileResource($this->whenLoaded('cover')),
            'sort' => $this->when(! is_null($this->sort), $this->sort),
            'count_course' => $this->when($request->routeIs('big-course-wechat-show'), function () {
                return $this->courses ? $this->courses->count() : 0;
            }),
            'count_course_price' => $this->when($request->routeIs('big-course-wechat-show'), function () {
                return $this->courses ? $this->courses->sum('price') : 0;
            }),
            'courses' => CourseResource::collection($this->whenLoaded('courses'))
        ];
    }
}
