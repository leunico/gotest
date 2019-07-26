<?php

namespace Modules\Personal\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\Transform;

class LearnProgressesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => (int) $this->id,
            'user_id' => (int) $this->user_id,
            'collect_learn_record_id' => (int) $this->collect_learn_record_id,
            'section_id' => (int) $this->section_id,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];

        return $data;
    }
}
