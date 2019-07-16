<?php

namespace Modules\Examinee\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class ExamineeDeviceProbingResource extends Resource
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
            'examination_examinee_id' => $this->examination_examinee_id,
            'is_camera' => $this->is_camera,
            'is_microphone' => $this->is_microphone,
            'is_chrome' => $this->is_chrome,
            'is_mc_ide' => $this->is_mc_ide,
            'is_scratch_ide' => $this->is_scratch_ide,
            'is_c_ide' => $this->is_c_ide,
            'is_python_ide' => $this->is_python_ide,
            'created_at' => $this->created_at
        ];
    }
}
