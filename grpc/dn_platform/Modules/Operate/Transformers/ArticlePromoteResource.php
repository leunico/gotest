<?php

namespace Modules\Operate\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\FileResource;

class ArticlePromoteResource extends Resource
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
            'article_id' => $this->article_id,
            'image' => new FileResource($this->whenLoaded('image')),
            'name' => $this->name,
            'wechat_number' => $this->wechat_number,
            'status' => $this->when($this->status, $this->status),
            'pv' => $this->when($this->pv, $this->pv),
            'uv' => $this->when($this->uv, $this->uv)
        ];
    }
}
