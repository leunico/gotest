<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = [
        'user_id',
        'province_id',
        'city_id',
        'district_id',
        'status',
        'receiver',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function province()
    {
        return $this->belongsTo(District::class, 'province_id', 'code');
    }

    public function city()
    {
        return $this->belongsTo(District::class, 'city_id', 'code');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'code');
    }
}
