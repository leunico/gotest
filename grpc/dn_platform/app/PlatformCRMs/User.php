<?php

namespace App\PlatformCRMs;

class User extends Model
{
    protected $guarded = ['id'];
    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
