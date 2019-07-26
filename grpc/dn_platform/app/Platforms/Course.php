<?php

namespace App\Platforms;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $guarded = ['id'];

    /*
     * 课程与年级段对应关系
     */
    protected $table = 'course';
}
