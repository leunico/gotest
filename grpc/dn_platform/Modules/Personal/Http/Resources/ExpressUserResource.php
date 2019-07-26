<?php

namespace Modules\Personal\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\Transform;

class ExpressUserResource extends JsonResource
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
            'user_id' => (int) $this->user_id,
            'course_id' => (int) $this->course_id,
            'course' => (object) $this->transformItem($this->whenLoaded('course'), CourseResource::class),
            'order_id' => (int) $this->order_id,
            'send_status' => (int) $this->send_status,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];

        return $data;
    }
}
