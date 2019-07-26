<?php

namespace Modules\Personal\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\Transform;

class DeliveryResource extends JsonResource
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
            'express_user_id' => (int) $this->express_user_id,
            'express_user' => (object) $this->transformItem($this->whenLoaded('expressUser'), ExpressUserResource::class),
            'operator_id' => (int) $this->operator_id,
            'operator' => (object) $this->transformItem($this->whenLoaded('operator'), UserResource::class),
            'category' => (int) $this->category,
            'receiver' => (string) $this->receiver,
            'province_id' => (int) $this->province_id,
            'province' => (object) $this->transformItem($this->whenLoaded('province'), DistrictsResource::class),
            'city_id' => (int) $this->city_id,
            'city' => (object) $this->transformItem($this->whenLoaded('city'), DistrictsResource::class),
            'district_id' => (int) $this->district_id,
            'district' => (object) $this->transformItem($this->whenLoaded('district'), DistrictsResource::class),
            'detail_address' => (string) $this->detail_address,
            'memo' => (string) $this->memo,
            'send_at' => (string) $this->send_at,
            'express_company' => (string) $this->express_company,
            'track_number' => (string) $this->track_number,
            'delivery_lessons' => $this->transformCollection($this->whenLoaded('deliveryLessons'), DeliveryLessonsResource::class),
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];

        return $data;
    }
}
