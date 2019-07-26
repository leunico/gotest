<?php

namespace Modules\Educational\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Modules\Course\Transformers\BiuniqueCourseResource;
use App\Http\Resources\FileResource;

class BiuniqueAppointmentRecource extends Resource
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
            'remark' => $this->remark,
            'lesson_sort' => $this->lesson_sort,
            'attendance' => $this->attendance,
            'biunique_course_id' => $this->biunique_course_id,
            'teacher_office_time_id' => $this->teacher_office_time_id,
            'teacher_id' => $this->teacher_id,
            'time' => $this->time,
            'type' => $this->type,
            'star_cost' => $this->star_cost,
            'appointment_date' => $this->appointment_date,
            'teacher_name' => $this->teacher_name,
            'teacher_sex' => $this->teacher_sex,
            'appointments_url' => $this->appointments_url,
            'biunique_course' => new BiuniqueCourseResource($this->whenLoaded('biuniqueCourse')),
            'teacher_office_time' => new TeacherOfficeTimeResource($this->whenLoaded('teacherOfficeTime')),
            'files' => FileResource::collection($this->whenLoaded('files'))
        ];
    }
}
