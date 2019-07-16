<?php

namespace Modules\Examinee\Entities;

use Illuminate\Database\Eloquent\Model;

class ExamineeTencentFace extends Model
{
    const CATEGORY_RECOGNITION = 1;
    const CATEGORY_COMPARE = 2;
    const TYPE_AFTER = 1;
    const TYPE_BEFORE = 2;

    protected $fillable = [];

    public static $categorys = [
        self::CATEGORY_RECOGNITION,
        self::CATEGORY_COMPARE
    ];
}
