<?php

namespace Modules\Operate\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class OrderGoodResource extends Resource
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
            'order_id' => $this->order_id,
            'goods_type' => $this->goods_type,
            'goods_id' => $this->goods_id,
            'goods_title' => $this->goods_title,
            'goods_price' => $this->goods_price,
            'payment_price' => $this->payment_price,
            'goods_attr' => $this->goods_attr,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
