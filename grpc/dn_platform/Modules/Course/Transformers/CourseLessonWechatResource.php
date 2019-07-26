<?php

namespace Modules\Course\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\FileResource;

class CourseLessonWechatResource extends Resource
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
            'cover' => new FileResource($this->whenLoaded('cover')),
            'lesson_intro' => $this->lesson_intro,
            'knowledge' => $this->knowledge,
        ];
    }
}
