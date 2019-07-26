<?php

namespace Modules\Educational\Entities;

use Illuminate\Database\Eloquent\Model;

class ClassStudent extends Model
{
    protected $fillable = [];

    public function courses()
    {
        return $this->hasMany(ClassCourse::class, 'class_id', 'class_id');
    }
}
