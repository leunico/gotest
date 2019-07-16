<?php

namespace Modules\Examinee\Entities;

use Illuminate\Database\Eloquent\Model;

class ExamineePush extends Model
{
    protected $fillable = [
        'examinee_id',
        'pushtable_type',
        'pushtable_id',
        'body',
    ];
}
