<?php

namespace Modules\Personal\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Operate\Entities\Order;

class UserIntroduce extends Model
{
    protected $fillable = [
        'user_id'
    ];

    public function courseUsers()
    {
        return $this->hasMany(CourseUser::class, 'user_id', 'user_id')
            ->select('user_id', 'course_id', 'order_id', 'memo', 'created_at', 'class_id', 'id')
            ->where('status', CourseUser::STATUS_NO);
    }

    public function courseLessons()
    {
        return $this->hasMany(UserCourseLesson::class, 'user_id', 'user_id')
            ->select('user_id', 'course_id', 'course_lesson_id', 'type', 'id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }
}
