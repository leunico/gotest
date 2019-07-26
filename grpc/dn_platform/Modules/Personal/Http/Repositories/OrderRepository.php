<?php

declare(strict_types=1);

namespace Modules\Personal\Http\Repositories;

use App\Http\Repositories\BaseRepository;
use Modules\Operate\Entities\Order;
use Illuminate\Support\Carbon;
use function App\isTradeNo;

class OrderRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = $this->model()
            ->leftJoin('users', 'users.id', '=', 'orders.creator_id')
            ->whereNull('deleted_at');
    }

    /**
     * @return \Modules\Operate\Entities\Order
     */
    public function model()
    {
        return new Order();
    }

    /**
     * 下单时间
     *
     * @param string|null $startDate 2018-01-01
     * @param string|null $endDate
     * @return \Modules\Personal\Http\Repositories\OrderRepository
     */
    public function date(?string $startDate, ?string $endDate): OrderRepository
    {
        if ($startDate !== null) {
            $this->model->where('orders.created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }

        if ($endDate !== null) {
            $this->model->where('orders.created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        return $this;
    }

    /**
     * 订单号搜索
     *
     * @param string|null $keyword
     * @return \Modules\Personal\Http\Repositories\OrderRepository
     */
    public function keyword(?string $keyword): OrderRepository
    {
        if ($keyword) {
            if (isTradeNo($keyword)) {
                $this->model->where('orders.trade_no', '=', $keyword);
            } else {
                $this->model->where('users.real_name', 'like', "{$keyword}%");
            }
        }

        return $this;
    }

    /**
     * 订单状态
     *
     * @param integer|null $status
     * @return \Modules\Personal\Http\Repositories\OrderRepository
     */
    public function status(?int $status): OrderRepository
    {
        if ($status !== null) {
            if ($status === Order::ORDER_PAID) {
                $this->model->where('orders.is_paid', '=', Order::PAID);
            } elseif ($status === Order::ORDER_UNPAID) {
                $this->model->where('orders.is_paid', '=', Order::UNPAID)
                    ->where('orders.expired_at', '<', Carbon::now());
            } elseif ($status === Order::ORDER_CLOSED) {
                $this->model->where('is_paid', '=', Order::UNPAID)
                    ->where('orders.expired_at', '>=', Carbon::now());
            }
        }

        return $this;
    }
}
