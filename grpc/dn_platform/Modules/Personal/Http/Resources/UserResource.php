<?php

namespace Modules\Personal\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\Transform;
use function App\formatSecond;
use App\User;

class UserResource extends JsonResource
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
            'real_name' => (string) $this->real_name,
            'email' => (string) $this->email,
            'phone' => (string) $this->phone,
            'sex' => (int) $this->sex,
            'avatar' => (string) $this->getAvatar(),
            'channel_id' => (int) $this->channel_id,
            'channel' => (object) $this->transformItem($this->whenLoaded('channel'), ChannelResource::class),
            'grade' => (int) $this->grade,
            'grade_msg' => (string) isset(User::$gradeMap[$this->grade]) ? User::$gradeMap[$this->grade] : '',
            'age' => (string) $this->age,
            'account_status' => (int) $this->account_status,
            'last_login_at' => (string) $this->last_login_at,
            'login_count' => (int) $this->login_count,
            'learn_records' => (array) [],
            'creator_id' => (int) $this->creator_id,
            'course_category' => (array) [],
            'learn_records' => (array) [],
            'works' => $this->transformCollection($this->whenLoaded('works'), WorksResource::class),
            'address' => (object) $this->transformItem($this->whenLoaded('address'), UserAddressResource::class),
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];

        // 用户类型，1: 付费用户, 0: 非付费用户
        if ($this->relationLoaded('userCategory') !== null) {
            $data['user_category'] = (int) (bool) $this->userCategory->count();
        }

        if ($this->relationLoaded('courseUsers')) {
            // 购买的课程类型
            $data['course_category'] = $this->courseUsers->pluck('course')->pluck('category')->unique()->values()->toArray();
        }

        if ($this->relationLoaded('learnRecords')) {
            $data['learn_records'] = $this->transformCollection($this->learnRecords, LearnRecordsResource::class);

            // 学习总时长
            $data['learn_records_total'] = formatSecond((int) floor($this->learnRecords->pluck('duration')->sum() / 1000));

            // 最近学习时长&学习时间
            $data['latest_learn_record'] = (object) $this->transformItem($this->learnRecords->last(), LearnRecordsResource::class);
        }

        return $data;
    }
}
