<?php

namespace Modules\Personal\Entities;

use Illuminate\Database\Eloquent\Model;
use App\User;

class LoginLog extends Model
{
    protected $table = 'login_logs';

    protected $guarded = [];

    protected $fillable = [
        'user_id',
        'ip',
        'device',
        'user_agent',
        'country',
        'province',
        'city',
        'district',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
