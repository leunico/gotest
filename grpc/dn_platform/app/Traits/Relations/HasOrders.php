<?php

declare(strict_types=1);

namespace App\Traits\Relations;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Personal\Entities\UserOrder;
use phpDocumentor\Reflection\Types\Boolean;
use Illuminate\Database\Eloquent\Model;

trait HasOrders
{
    /**
     * is user orders
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     * @author lizx
     */
    public function userOrders(): HasMany
    {
        return $this->hasMany(UserOrder::class);
    }

    /**
     * 添加一条流水记录
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param integer $type
     * @param integer $amount
     * @param string $describe
     * @return \Illuminate\Database\Eloquent\Model|false
     * @author lizx
     */
    public function addUserOrder(Model $model, int $type, int $amount, string $describe = '')
    {
        // todo 为零不记录流水
        if (empty($amount)) {
            return true;
        }

        $userOrder = new UserOrder;
        $userOrder->creator_id = $model->creator_id ?? 0;
        $userOrder->target_id = $model->id ?? 0;
        $userOrder->target_type = get_class($model);
        $userOrder->describe = $describe;
        $userOrder->type = $type;
        $userOrder->amount = $amount;
        $userOrder->category = UserOrder::CATEGORY_STAR;

        return $this->userOrders()->save($userOrder);
    }
}
