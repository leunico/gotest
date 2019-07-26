<?php

namespace Modules\Course\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StarPackage extends Model
{
    use SoftDeletes;

    protected $fillable = [];

    const STATUS_PUBLISH = 1;

    public function actionStatus()
    {
        return empty($this->status) ? $this->increment('status') : $this->decrement('status');
    }

    /**
     * 设定价格
     *
     * @param  string  $value
     * @return void
     */
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = $value * 100;
    }

    /**
     * 获取价格
     *
     * @param  string  $value
     * @return void
     */
    public function getPriceAttribute($value)
    {
        return $value / 100;
    }
}
