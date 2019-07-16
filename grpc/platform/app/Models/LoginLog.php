<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    protected $fillable = [
        'user_id',
        'ip',
        'device',
        'user_agent',
        'country',
        'province',
        'city',
        'district',
        'examination_examinee_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
