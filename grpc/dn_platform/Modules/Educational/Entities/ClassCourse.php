<?php

namespace Modules\Educational\Entities;

use Illuminate\Database\Eloquent\Model;

class ClassCourse extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'course_id',
        'class_id'
    ];
}
