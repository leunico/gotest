<?php

namespace Modules\Personal\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\Transform;
use Modules\Course\Transformers\CourseLessonResource;

class DeliveryLessonsResource extends JsonResource
{
    use Transform;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => (int) $this->id,
            'delivery_id' => (int) $this->delivery_id,
            'lesson_id' => (int) $this->lesson_id,
            'lesson' => (object) $this->transformItem($this->lesson, CourseLessonResource::class),
        ];

        return $data;
    }
}
