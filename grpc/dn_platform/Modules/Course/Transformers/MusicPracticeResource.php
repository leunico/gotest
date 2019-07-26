<?php

namespace Modules\Course\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class MusicPracticeResource extends Resource
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
            'name' => $this->name,
            'audio_link' => $this->audio_link,
            'book' => $this->whenLoaded('book'),
            'tags' => $this->whenLoaded('tags'),
            'status' => $this->status,
        ];
    }
}
