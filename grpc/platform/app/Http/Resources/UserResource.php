<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'remarks' => $this->remarks,
            'is_admin' => $this->hasRole('admin'),
            'roles' => $this->when(! $this->hasRole('admin'), $this->getRoleNames()),
            'permissions' => $this->when(! $this->hasRole('admin'), $this->getAllPermissions()->pluck('name')),
        ];
    }
}
