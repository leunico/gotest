<?php

namespace Modules\Personal\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\Transform;
use Modules\Operate\Entities\Order;

class OrderResource extends JsonResource
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
        return [
            'id' => (int) $this->id,
            'user_id' => (int) $this->user_id,
            'user' => (object) $this->transformItem($this->whenLoaded('user'), UserResource::class),
            'trade_no' => (string) $this->trade_no,
            'payment_method' => (int) $this->payment_method,
            'discount' => (int) $this->discount,
            'total_price' => (int) $this->total_price,
            'real_price' => (int) $this->real_price,
            'is_paid' => (int) $this->is_paid,
            'paid_at' => (string) $this->paid_at,
            'memo' => (string) $this->memo,
            'expired_at' => (string) $this->expired_at,
            'ext_data' => (string) $this->ext_data,
            'prepay_id' => (string) $this->prepay_id,
            'trade_type' => (string) $this->trade_type,
            'tx_num' => (string) $this->tx_num,
            'creator_id' => (int) $this->creator_id,
            'creator' => (object) $this->transformItem($this->whenLoaded('creator'), UserResource::class),
            'status' => (int) $this->status(),
            'status_msg' => (string) Order::$payStatusMap[$this->status()],
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];
    }
}
