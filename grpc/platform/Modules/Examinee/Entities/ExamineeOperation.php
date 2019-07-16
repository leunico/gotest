<?php

namespace Modules\Examinee\Entities;

use Illuminate\Database\Eloquent\Model;

class ExamineeOperation extends Model
{
    const CATEGORY_NOTHING = 0;
    const CATEGORY_CUTTING_SCREEN = 1;
    const CATEGORY_FACE = 2;
    const CATEGORY_OFFLINE = 3;

    protected $fillable = [
        'examination_examinee_id',
        'category',
        'remark',
        'source_id',
    ];

    public static $categorys = [
        self::CATEGORY_CUTTING_SCREEN,
        self::CATEGORY_FACE,
        self::CATEGORY_OFFLINE,
    ];
}
