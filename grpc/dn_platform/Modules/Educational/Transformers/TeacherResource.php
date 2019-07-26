<?php

namespace Modules\Educational\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\FileResource;
use Illuminate\Http\Resources\PotentiallyMissing;

class TeacherResource extends Resource
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
            'user_id' => $this->user_id,
            'type' => $this->type,
            'qrcode' => $this->when(! ($this->whenLoaded('qrcodeFile') instanceof PotentiallyMissing), function () {
                return new FileResource($this->qrcodeFile);
            }, null)
        ];
    }
}
