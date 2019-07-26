<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\User;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'real_name' => $this->real_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'sex' => $this->sex,
            'avatar' => $this->getAvatar(),
            'age' => $this->age,
            'grade' => $this->grade,
            'grade_msg' => isset(User::$gradeMap[$this->grade]) ? User::$gradeMap[$this->grade] : null,
            'unionid' => $this->unionid,
            'is_address' => $this->is_address,
            'is_website_openid' => $this->when($this->wechatUser, function () {
                return ! empty($this->wechatUser->website_openid);
            }, false),
            'is_art_openid' => $this->when($this->wechatUser, function () {
                return ! empty($this->wechatUser->art_openid);
            }, false),
            'is_music_openid' => $this->when($this->wechatUser, function () {
                return ! empty($this->wechatUser->music_openid);
            }, false),
            'is_admin' => $this->hasRole('admin'),
            'roles' => $this->when(! $this->hasRole('admin'), $this->getRoleNames()),
            'permissions' => $this->when(! $this->hasRole('admin'), $this->getAllPermissions()->pluck('name')),
            'user_address' => new UserAddressResource($this->address),
        ];
    }
}
