<?php

namespace Modules\Examinee\Entities;

use Illuminate\Database\Eloquent\Model;

class ExamineeVideo extends Model
{
    const TYPE_BEFORE = 0;
    const TYPE_VERIFICATION = 1;
    const TYPE_EXAMINATION = 2;
    
    public static $types = [
        self::TYPE_BEFORE,
        self::TYPE_VERIFICATION,
        self::TYPE_EXAMINATION,
    ];
}
