<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait ExtendModel
{
    /**
     * 批量更新一条字段
     *
     * @param array $update
     * @param string $setField
     * @param string $whenField
     * @return miexd
     */
    public function batchUpdate(array $update, $setField = 'sort', $whenField = 'id')
    {
        try {
            $when = $thens = [];
            foreach ($update as $id => $value) {
                $when[] = sprintf("WHEN %d THEN %d ", $id, $value);
            }
            $thens[$setField] = DB::raw("CASE {$whenField} " . implode(' ', $when) . ' END ');

            DB::table($this->getTable())->whereIn($whenField, array_keys($update))->update($thens);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
