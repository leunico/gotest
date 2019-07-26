<?php

namespace App\PlatformCRMs;

class Customer extends Model
{
    protected $guarded = ['id'];

    protected $dates = ['expired_at', 'be_customer_at', 'communicated_at'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
