<?php

namespace Modules\Personal\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\Transform;
use function App\formatSecond;

class LearnRecordsResource extends JsonResource
{
    use Transform;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'user_id' => (int) $this->user_id,
            'user' => (object) $this->transformItem($this->whenLoaded('user'), UserResource::class),
            'section_id' => (int) $this->section_id,
            'section' => (object) $this->transformItem($this->whenLoaded('courseSection'), SectionResource::class),
            'music_id' => (int) $this->music_id,
            'entry_at' => (string) $this->entry_at,
            'leave_at' => (string) $this->leave_at,
            'start_at' => (int) $this->start_at,
            'end_at' => (int) $this->end_at,
            'duration' => (int) $this->duration,
            'duration_format' => formatSecond((float) ($this->duration / 1000)),
        ];
    }
}
