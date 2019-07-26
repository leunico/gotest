<?php

namespace Modules\Personal\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\Transform;

class OrderGoodResource extends JsonResource
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
            'order_id' => (string) $this->order_id,
            'goods_type' => (string) $this->goods_type,
            'goods_id' => (int) $this->goods_id,
            'goods_title' => (string) $this->goods_title,
            'goods_price' => (int) $this->goods_price,
            'payment_price' => (int) $this->payment_price,
            'goods_attr' => (int) $this->goods_attr,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];

        return $data;
    }
}
