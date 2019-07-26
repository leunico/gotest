<?php

namespace Modules\Personal\Entities;

use Illuminate\Database\Eloquent\Model;
use App\User;

class UserOrder extends Model
{
    const TYPE_ZC = -1;
    const TYPE_SL = 1;

    protected $guarded = [];

    const CATEGORY_STAR = 1;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpenditure()
    {
        return $this->type == self::TYPE_ZC;
    }

    public function isIncome()
    {
        return $this->type == self::TYPE_SL;
    }
}
