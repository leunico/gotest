<?php

namespace Modules\Educational\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class TeacherOfficeTimeResource extends Resource
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
            'time' => $this->time,
            'appointment_date' => $this->appointment_date,
            'end_date' => $this->end_date,
            'type' => $this->type,
            'sort' => $this->sort,
            'user' => $this->whenLoaded('user')
        ];
    }
}
