<?php

namespace Modules\Operate\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Modules\Educational\Entities\BiuniqueAppointment;

class StarPackageUser extends Model
{
    const STATUS_NO = 1;
    const STATUS_OFF = 0;

    protected $fillable = [];

    public function biuniqueAppointments()
    {
        return $this->hasMany(BiuniqueAppointment::class, 'user_id', 'user_id');
    }

    public static function assign(Order $order)
    {
        if (! $order->isPaid()) {
//            \Log::notice('尝试给未支付的订单分配课程权限：' . $order->trade_no);
            return false;
        }

        $user = $order->user;
        $now = Carbon::now();
        $data = [];
        foreach ($order->goods as $good) {
            if ($good->goods_type == Order::GOODS_TYPE_STAR_PACKAGE) {
                $data[] = [
                    'user_id' => $user->id,
                    'star_package_id' => $good->goods_id,
                    'creator_id' => $order->creator_id,
                    'order_id' => $order->id,
                    'star' => $good->star,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }
        }

        StarPackageUser::insert($data);
        return true;
    }
}
