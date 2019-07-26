<?php

namespace Modules\Personal\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Course\Entities\CourseLesson;

class DeliveryLesson extends Model
{
    protected $guarded = [];

    public function lesson()
    {
        return $this->hasOne(CourseLesson::class, 'id', 'lesson_id');
    }
}
