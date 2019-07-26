<?php

namespace Modules\Operate\Entities;

use Illuminate\Database\Eloquent\Model;

class PaymentData extends Model
{
    protected $table = 'payment_data';

    protected $fillable = ['tx_num','payment_method','tx_data'];

    public $timestamps = false;

    protected $casts = [
        'tx_data' => 'json',
    ];
}
