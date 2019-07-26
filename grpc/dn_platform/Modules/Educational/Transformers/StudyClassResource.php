<?php

namespace Modules\Educational\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\UserTeacherResource;
use Illuminate\Support\Carbon;

class StudyClassResource extends Resource
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
            'id' => (int) $this->id,
            'name' => (string) $this->name,
            // todo需要大于开课日期才返回！
            'teacher' => $this->when((! $this->whenLoaded('teacher') instanceof PotentiallyMissing) && Carbon::now()->gte(Carbon::parse($this->entry_at)), function () {
                return new UserTeacherResource($this->teacher);
            }, null)
        ];
    }
}
