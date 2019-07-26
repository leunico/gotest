<?php

namespace Modules\Personal\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $data = [
            'id' => (int) $this->id,
            'name' => (string) $this->name,
            'real_name' => (string) $this->real_name,
            'phone' => (string) $this->phone,
            'sex' => (int) $this->sex,
            'grade' => (int) $this->grade,
            'avatar' => (string) asset($this->avatar),
            'user_category' => (int) $this->user_category,
            'login_count' => (int) $this->login_count,
            'account_status' => (int) $this->account_status,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];

        // 用户渠道来源
        if (isset($this->channel_title)) {
            $data['channel_title'] = $this->channel_title;
        }

        // 购买的课程类型
        if (isset($this->courseUsers)) {
            $data['course_category'] = $this->courseUsers->pluck('course')->pluck('category')->unique()->values()->toArray();
        }

        return $data;
    }
}
