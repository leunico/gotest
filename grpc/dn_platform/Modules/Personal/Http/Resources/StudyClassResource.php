<?php

namespace Modules\Personal\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\Transform;
use function App\formatSecond;
use App\User;

class StudyClassResource extends JsonResource
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
        $data = [
            'id' => (int) $this->id,
            'name' => (string) $this->name,
            'entry_at' => (string) $this->entry_at,
            'leave_at' => (string) $this->entry_at,
            'category' => (int) $this->category,
            'pattern' => (int) $this->pattern,
            'frequency' => (int) $this->frequency,
            'unlocak_times' => (array) $this->unlocak_times,
            'big_course_id' => (int) $this->big_course_id,
            'course_id' => (int) $this->course_id,
            'teacher_id' => (int) $this->teacher_id,
            'status' => (int) $this->status,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];

        if ($this->relationLoaded('teacher') !== null) {
            $data['teacher'] = (object) $this->transformItem($this->teacher, UserResource::class);
        }

        if ($this->relationLoaded('students') !== null) {
            $data['students'] = $this->transformCollection($this->students, UserResource::class);

            // 学习总时长
            $data['learn_records_total'] = formatSecond((int) $this->students->reduce(function ($total, $item) {
                return $total + (int) floor($item->learnRecords->pluck('duration')->sum());
            })/ 1000);
        }

        if ($this->relationLoaded('students_count') !== null) {
            $data['students_count'] = (int) $this->students_count;
        }

        return $data;
    }
}
