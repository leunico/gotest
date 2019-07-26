<?php

declare(strict_types=1);

namespace Modules\Personal\Http\Repositories;

use App\User;
use App\Http\Repositories\BaseRepository;
use Illuminate\Support\Carbon;
use Modules\Operate\Entities\Order;

class UserLearnRecordRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = $this->model()
            ->leftJoin('orders', 'orders.user_id', '=', 'users.id')
            ->leftJoin('learn_records', 'learn_records.user_id', '=', 'users.id')
            ->whereNull('orders.deleted_at')
            ->whereNotNull('learn_records.id');
    }

    /**
     * @return \App\User
     */
    public function model()
    {
        return new User();
    }

    /**
     * 年级
     *
     * @param integer|null $startGrade
     * @param integer|null $endGrade
     * @return \Modules\Personal\Http\Controllers\Apis\UserLearnRecordRepository
     */
    public function grade(?int $startGrade, ?int $endGrade): UserLearnRecordRepository
    {
        if ($startGrade !== null) {
            $this->model->where('users.grade', '>=', $startGrade);
        }

        if ($endGrade !== null) {
            $this->model->where('users.grade', '<=', $endGrade);
        }

        return $this;
    }

    /**
     * 性别
     *
     * @param integer|null $sex
     * @return \Modules\Personal\Http\Controllers\Apis\UserLearnRecordRepository
     */
    public function sex(?int $sex): UserLearnRecordRepository
    {
        if ($sex !== null) {
            $this->model->where('users.sex', '=', $sex);
        }

        return $this;
    }

    /**
     *  用户分类，是否是付费用户
     *
     * @param integer|null $catgory
     * @return \Modules\Personal\Http\Controllers\Apis\UserLearnRecordRepository
     */
    public function category(?int $catgory): UserLearnRecordRepository
    {
        if ($catgory !== null) {
            // 订单支付则是付费用户，否则是非付费用户
            if ($catgory === Order::PAID) {
                $this->model->where('orders.is_paid', '=', Order::PAID);
            } else {
                $this->model->whereDoesntHave('orders', function ($query) {
                    $query->where('orders.is_paid', '=', Order::PAID);
                });
            }
        }

        return $this;
    }

    /**
     * 最近学习时间
     *
     * @param string|null $startDate 2018-01-01
     * @param string|null $endDate
     * @return \Modules\Personal\Http\Controllers\Apis\UserLearnRecordRepository
     */
    public function date(?string $startDate, ?string $endDate): UserLearnRecordRepository
    {
        if ($startDate !== null) {
            $this->model->where('learn_records.entry_at', '>=', Carbon::parse($startDate)->startOfDay());
        }

        if ($endDate !== null) {
            $this->model->where('learn_records.entry_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        return $this;
    }

    /**
     * 关键词搜索
     *
     * @param string|null $keyword
     * @return \Modules\Personal\Http\Controllers\Apis\UserLearnRecordRepository
     */
    public function keyword(?string $keyword): UserLearnRecordRepository
    {
        if ($keyword !== null) {
            $this->model->keyword($keyword);
        }

        return $this;
    }
}
