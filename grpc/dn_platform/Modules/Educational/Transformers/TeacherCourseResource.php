<?php

namespace Modules\Educational\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\UserResource;

class TeacherCourseResource extends Resource
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
            'biunique_course_id' => $this->biunique_course_id,
            'default_sort' => $this->default_sort,
            'appointment_date' => $this->appointment_date,
            'time' => $this->time,
            'office_time_id' => $this->office_time_id,
            'status' => $this->status,
            'sort' => $this->sort,
            'user' => $this->whenLoaded('user'),
            'is_appointment' => $this->whenLoaded('biuniqueAppointment')
        ];
    }
}
