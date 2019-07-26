<?php

namespace Modules\Personal\Entities;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\District;

class Delivery extends Model
{
    const SEND_STATUS_WAIT = 1;
    const SEND_STATUS_PART = 2;
    const SEND_STATUS_FINISH = 3;

    protected $guarded = [];

    public function expressUser()
    {
        return $this->hasOne(ExpressUser::class, 'id', 'express_user_id');
    }

    public function operator()
    {
        return $this->hasOne(User::class, 'id', 'operator_id');
    }

    public function province()
    {
        return $this->hasOne(District::class, 'code', 'province_id');
    }

    public function city()
    {
        return $this->hasOne(District::class, 'code', 'city_id');
    }

    public function district()
    {
        return $this->hasOne(District::class, 'code', 'district_id');
    }

    public function deliveryLessons()
    {
        return $this->hasMany(DeliveryLesson::class, 'delivery_id', 'id');
    }

    public function lessons()
    {
        return $this->hasMany(DeliveryLesson::class);
    }

    public function operator_user()
    {
        return $this->belongsTo(User::class, 'operator_id', 'id');
    }
}
