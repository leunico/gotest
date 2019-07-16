<?php

namespace Modules\Examinee\Entities;

use Illuminate\Database\Eloquent\Model;

class ExamineeTechnicalSupport extends Model
{
    const STATUS_OK = 2;
    const STATUS_OFF = 0;
    const STATUS_IN = 1;

    protected $fillable = [];
}
