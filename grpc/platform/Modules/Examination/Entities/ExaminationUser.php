<?php

namespace Modules\Examination\Entities;

use Illuminate\Database\Eloquent\Model;
use function App\toDecbin;

class ExaminationUser extends Model
{
    /**
     * 设定type
     *
     * @param  array $value
     * @return void
     */
    public function setTypeAttribute(array $value)
    {
        $this->attributes['authority'] = array_sum($value);
    }

    /**
     * 获取type
     *
     * @param  int $value
     * @return array
     */
    public function getTypeAttribute($value)
    {
        return toDecbin($value);
    }
}
