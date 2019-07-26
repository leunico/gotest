<?php

namespace Modules\Operate\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class LeadResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
