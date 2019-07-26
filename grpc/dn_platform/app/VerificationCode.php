<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VerificationCode extends Model
{
    use SoftDeletes;

    const STATE_ON = 0;

    const STATE_OFF = 1;

    protected $fillable = ['state'];
}
