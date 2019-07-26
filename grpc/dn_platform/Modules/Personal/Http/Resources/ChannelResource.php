<?php

namespace Modules\Personal\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChannelResource extends JsonResource
{
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
            'category' => (int) $this->category,
            'owner_id' => (int) $this->owner_id,
            'level' => (int) $this->level,
            'level1_id' => (int) $this->level1_id,
            'level2_id' => (int) $this->level2_id,
            'level3_id' => (int) $this->level3_id,
            'parent_id' => (int) $this->parent_id,
            'title' => (string) $this->title,
            'description' => (string) $this->description,
            'link' => (string) $this->link,
            'slug' => (string) $this->slug,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];
    }
}
