<?php

namespace Modules\Course\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class MusicTheoryResource extends Resource
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
            'sort' => $this->sort,
            'source_link' => $this->source_link,
            'source_duration' => $this->source_duration,
            'status' => $this->status,
            'is_learn_music_progress' => $this->musicLearnRecords->isNotEmpty(),
            'music_learn_records' => $this->musicLearnRecords->last(),
            'pivot' => $this->pivot,
        ];
    }
}
