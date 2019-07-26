<?php

declare(strict_types=1);

namespace Modules\Personal\Http\Repositories;

use App\User;
use App\Http\Repositories\BaseRepository;

class UserAllDetailRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = $this->model()
            ->leftJoin('orders', 'orders.user_id', '=', 'users.id');
    }

    /**
     * @return \App\User
     */
    public function model()
    {
        return new User();
    }

    /**
     * 性别
     *
     * @param integer|null $sex
     * @return \Modules\Personal\Http\Controllers\Apis\UserAllDetailRepository
     */
    public function sex(?int $sex): UserAllDetailRepository
    {
        if ($sex !== null) {
            $this->model->where('users.sex', '=', $sex);
        }

        return $this;
    }

    /**
     * 关键词搜索
     *
     * @param string|null $keyword
     * @return \Modules\Personal\Http\Controllers\Apis\UserAllDetailRepository
     */
    public function keyword(?string $keyword): UserAllDetailRepository
    {
        if ($keyword !== null) {
            $this->model->keyword($keyword);
        }

        return $this;
    }
}
