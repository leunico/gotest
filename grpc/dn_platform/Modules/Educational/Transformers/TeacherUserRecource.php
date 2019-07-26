<?php

namespace Modules\Educational\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class TeacherUserRecource extends Resource
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
            'real_name' => $this->real_name,
            'email' => $this->email,
            'sex' => $this->sex,
            'avatar' => $this->getAvatar(),
            'teacher' => $this->whenLoaded('teacher')
        ];
    }
}
