<?php

declare(strict_types=1);

namespace Modules\Educational\Http\Controllers\Concerns;

use Illuminate\Support\Collection;
use function App\arrayKeyFirst;

trait ControllerExtend
{
    /**
     * 递归处理解锁日期排序
     *
     * @param array $unlocks
     * @param \Illuminate\Support\Collection $data
     * @param string $supplement
     * @return \Illuminate\Support\Collection
     */
    public function recursionUnlock($unlocks, &$data, $supplement): Collection
    {
        if (empty($unlocks)) {
            return $data;
        }

        $first = array_shift($unlocks);
        $nextDate = empty($unlocks) ? $supplement : arrayKeyFirst($unlocks);
        foreach ($first as $value) {
            $value['unlock_day'] = $nextDate;
            $data->push($value);
        }

        return $this->recursionUnlock($unlocks, $data, $supplement);
    }
}
