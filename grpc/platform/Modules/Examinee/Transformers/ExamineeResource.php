<?php

namespace Modules\Examinee\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class ExamineeResource extends Resource
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
            'email' => $this->email,
            'phone' => $this->phone,
            'sex' => $this->sex,
            'photo' => $this->photo,
            'certificate_type' => $this->certificate_type,
            'certificates' => $this->certificates,
            'admission_ticket' => $this->admission_ticket
        ];
    }
}
