<?php

namespace Modules\Educational\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Modules\Educational\Entities\Teacher;
use Illuminate\Support\Carbon;
use App\Http\Resources\UserResource;
use Modules\Educational\Entities\AuditionClass;

class AuditionClassResource extends Resource
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
            'category' => $this->category,
            'categoryStr' => Teacher::$authoritys[$this->category],
            'teacher' => $this->whenLoaded('teacher'),
            'entry_at' => $this->entry_at,
            'leave_at' => $this->leave_at,
            'status' => empty($this->status) ? $this->status : (Carbon::now()->gte(Carbon::parse($this->leave_at)) ? AuditionClass::STATUS_OVER : AuditionClass::STATUS_NO),
            'is_live_start' => (Carbon::now()->gte(Carbon::parse($this->entry_at)->subMinutes(10)) && Carbon::now()->lte(Carbon::parse($this->leave_at)->addMinutes(30))),
            'url' => config('educational.live.live_web_host') . '?class_id=' . $this->id
        ];
    }
}
