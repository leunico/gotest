<?php

namespace Modules\Course\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Storage;

class ArduinoMaterialResource extends Resource
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
            'info' => json_decode($this->info, true),
            'source_link' => $this->source_link,
            'md5' => $this->md5,
            'category' => $this->is_arduino,
            'type' => 'arduino',
            'tags' => [$this->is_arduino == 1 ? '元件宝典' : '艺术宝库'],
            'disk_url' => Storage::disk(config('filesystems.cloud'))->url('scratch/media/')
        ];
    }
}
