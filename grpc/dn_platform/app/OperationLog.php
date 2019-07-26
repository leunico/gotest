<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OperationLog extends Model
{
    protected $casts = [
        'old' => 'array',
        'new' => 'array',
    ];
}
