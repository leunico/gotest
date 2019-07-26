<?php

namespace Modules\Operate\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class OrderResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        //todo userResource
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'trade_no' => $this->trade_no,
            'payment_method' => $this->payment_method,
            'discount' => $this->discount,
            'total_price' => $this->total_price,
            'real_price' => $this->real_price,
            'is_paid' => $this->is_paid,
            'paid_at' => $this->paid_at,
            'memo' => $this->memo,
            'expired_at' => $this->expired_at,
            'ext_data' => $this->ext_data,
            'prepay_id' => $this->prepay_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'goods' => OrderGoodResource::collection($this->goods)
        ];
    }
}
