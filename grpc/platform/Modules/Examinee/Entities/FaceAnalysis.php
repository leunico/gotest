<?php

namespace Modules\Examinee\Entities;

use Illuminate\Database\Eloquent\Model;

class FaceAnalysis extends Model
{
    /**
     * 应该被转换成原生类型的属性。
     *
     * @var array
     */
    protected $casts = [
        'headpose' => 'json',
        'blur' => 'json',
        'eyegaze_left' => 'json',
        'eyegaze_right' => 'json',
    ];
}
