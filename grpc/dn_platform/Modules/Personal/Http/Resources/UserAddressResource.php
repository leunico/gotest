<?php

namespace Modules\Personal\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\Transform;

class UserAddressResource extends JsonResource
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
            'user_id' => (int) $this->user_id,
            'province_id' => (int) $this->province_id,
            'province' => (object) $this->transformItem($this->whenLoaded('province'), DistrictsResource::class),
            'city_id' => (int) $this->city_id,
            'city' => (object) $this->transformItem($this->whenLoaded('city'), DistrictsResource::class),
            'district_id' => (int) $this->district_id,
            'district' => (object) $this->transformItem($this->whenLoaded('district'), DistrictsResource::class),
            'detail' => (string) $this->detail,
            'receiver' => (string) $this->receiver,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];

        return $data;
    }
}
