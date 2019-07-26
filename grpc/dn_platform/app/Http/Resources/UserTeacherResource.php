<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Educational\Transformers\TeacherResource;
use Illuminate\Http\Resources\PotentiallyMissing;

class UserTeacherResource extends JsonResource
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
            'name' => $this->name,
            'real_name' => $this->real_name,
            'phone' => $this->phone,
            'sex' => $this->sex,
            'teacher' => $this->when(! ($this->whenLoaded('teacher') instanceof PotentiallyMissing), function () {
                return new TeacherResource($this->teacher);
            }, null)
        ];
    }
}
