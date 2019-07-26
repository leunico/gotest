<?php

namespace App\PlatformCRMs;

use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerContact extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    /**
     * 关联客户
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function customerUser()
    {
        return $this->hasOne(CustomerUser::class, 'wwwuser_id', 'www_user_id');
    }
}
