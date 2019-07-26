<?php

namespace Modules\Course\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class CourseSectionResource extends Resource
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
            'title' => $this->title,
            'course_lesson_id' => $this->course_lesson_id,
            'section_intro' => $this->section_intro,
            'source_link' => $this->source_link,
            'source_duration' => $this->source_duration,
            'category' => $this->category,
            'learn_progresses' => $this->learnProgresses->isNotEmpty(),
            'learn_records' => $this->learnRecords->last(),
            // 'arduino_material_id' => $this->arduino_material_id,
            // 'arduino_material' => new ArduinoMaterialResource($this->arduinoMaterial), //todo 不要这个了
            'problems' => ProblemResource::collection($this->problems),
        ];
    }
}
