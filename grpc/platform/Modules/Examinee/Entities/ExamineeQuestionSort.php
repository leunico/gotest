<?php

namespace Modules\Examinee\Entities;

use Illuminate\Database\Eloquent\Model;

class ExamineeQuestionSort extends Model
{
    protected $fillable = [
        'examination_examinee_id',
        'sorttable_id',
        'sorttable_type',
        'sort',
    ];
}
