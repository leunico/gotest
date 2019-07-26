<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'driver_baseurl' => $this->driver_baseurl,
            'origin_filename' => $this->origin_filename,
            'filename' => $this->filename,
            'mime' => $this->mime,
        ];
    }
}
