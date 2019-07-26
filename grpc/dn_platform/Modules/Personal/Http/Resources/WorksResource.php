<?php

namespace Modules\Personal\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\Transform;

class WorksResource extends JsonResource
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
        return [
            'id' => (int) $this->id,
            'title' => (string) $this->title,
            'user_id' => (int) $this->user_id,
            'user' => (object) $this->transformItem($this->whenLoaded('user'), UserResource::class),
            'image_cover' => (string) $this->image_cover,
            'description' => (string) $this->image_cover,
            'type' => (string) $this->type,
            'board_type' => (string) $this->board_type,
            'lesson_id' => (int) $this->lesson_id,
            'lesson' => (object) $this->transformItem($this->whenLoaded('lesson'), LessonResource::class),
            'file_url' => (string) $this->file_url,
            'views' => (int) $this->views,
            'comments' => (int) $this->comments,
            'likes' => (int) $this->likes,
            'status' => (int) $this->status,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];
    }
}
