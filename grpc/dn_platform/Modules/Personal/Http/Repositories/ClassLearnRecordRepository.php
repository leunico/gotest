<?php

declare(strict_types=1);

namespace Modules\Personal\Http\Repositories;

use App\User;
use App\Http\Repositories\BaseRepository;
use Illuminate\Support\Carbon;
use Modules\Operate\Entities\Order;
use Modules\Educational\Entities\StudyClass;

class ClassLearnRecordRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = $this->model()
            ->whereNull('deleted_at');
    }

    /**
     * @return \Modules\Educational\Entities\StudyClass
     */
    public function model()
    {
        return new StudyClass();
    }

    /**
     * 开班时间
     *
     * @param string|null $startDate 2018-01-01
     * @param string|null $endDate
     * @return \Modules\Personal\Http\Controllers\Apis\ClassLearnRecordRepository
     */
    public function date(?string $startDate, ?string $endDate): ClassLearnRecordRepository
    {
        if ($startDate !== null) {
            $this->model->where('entry_at', '>=', Carbon::parse($startDate)->startOfDay());
        }

        if ($endDate !== null) {
            $this->model->where('entry_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        return $this;
    }

    /**
     * 关键词搜索
     *
     * @param string|null $keyword
     * @return \Modules\Personal\Http\Controllers\Apis\ClassLearnRecordRepository
     */
    public function keyword(?string $keyword): ClassLearnRecordRepository
    {
        if ($keyword !== null) {
            $this->model->where('name', 'like', "{$keyword}%");
        }

        return $this;
    }

    /**
     * 班主任筛选
     *
     * @param integer $teacherId
     * @return \Modules\Personal\Http\Controllers\Apis\ClassLearnRecordRepository
     */
    public function teacher(int $teacherId): ClassLearnRecordRepository
    {
        if ($teacherId) {
            $this->model->where('teacher_id', '=', $teacherId);
        }

        return $this;
    }
}
