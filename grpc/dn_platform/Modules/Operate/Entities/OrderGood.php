<?php

namespace Modules\Operate\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Course\Entities\Course;

class OrderGood extends Model
{
    protected $guarded = ['id'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'goods_id');
    }
}
