<?php

namespace Modules\Course\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\Personal\Entities\Work;
use App\Http\Resources\FileResource;
use Illuminate\Support\Carbon;

class CourseLessonCourseResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        if ($request->routeIs('course-show')) {
            $learnProgresses = $this->userLearnRecord &&
                $this->userLearnRecord->learnProgresses ? $this->userLearnRecord->learnProgresses->pluck('section_id') :
                collect([]);
            $this->sections->map(function ($val) use ($learnProgresses) {
                $val->is_learn_section = $learnProgresses->contains($val->id);
            });
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'cover' => new FileResource($this->whenLoaded('cover')),
            'is_code' => $this->is_code,
            'is_drainage' => $this->is_drainage,
            'disk_url' => Storage::disk(config('filesystems.cloud'))->url($request->routeIs('course-show') ? '' : 'scratch/media/'),
            'course_id' => $this->course_id,
            'count_user_learns' => (int) ($this->count_user_learns + $this->learn_records_count),
            'count_lesson_work' => $this->when($this->whenLoaded('works'), function () {
                return $this->works->count();
            }, 0),
            'last_lesson_work' => $this->when($this->whenLoaded('works'), function () {
                return $this->works->last();
            }, null),
            'is_clock' => $this->when($this->whenLoaded('works'), function () {
                return $this->works->contains('share', Work::SHARE_STATUS_ON);
            }, false),
            'is_learn_lesson' => $this->when($this->whenLoaded('userLearnRecord'), function () {
                return $this->userLearnRecord->status;
            }, false),
            // 'is_unlock' => $this->when($this->whenLoaded('unlockDays'), function () {
            //     return $this->unlockDays ? Carbon::now()->gte(Carbon::parse($this->unlockDays->unlock_day)) : false;
            // }, false),
            'is_unlock' => $this->is_unlock,
            'sections' => $this->sections, // todo 只是显示是否学习过
        ];
    }
}
