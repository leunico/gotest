<?php

namespace Modules\Operate\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class BannerResource extends Resource
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
            'number' => $this->number,
            'link' => $this->link,
            'platform' => $this->platform,
            'image_url' => $this->image_url,
        ];
    }
}
