<?php

namespace Modules\Personal\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class WorkResource extends Resource
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
            'image_cover' => $this->image_cover,
            'description' => $this->description,
            'type' => $this->type,
            'board_type' => $this->board_type,
            'file_url' => $this->file_url,
            'views' => $this->views,
            'comments' => $this->comments,
            'likes' => $this->likes,
            'created_at' => $this->created_at->format('Y-m-d')
        ];
    }
}
