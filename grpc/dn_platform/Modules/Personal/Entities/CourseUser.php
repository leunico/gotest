<?php

namespace Modules\Personal\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Course\Entities\CourseLesson;
use Modules\Course\Entities\Course;
use Modules\Operate\Entities\Order;
use Modules\Course\Entities\BigCourse;
use App\User;

class CourseUser extends Model
{
    use SoftDeletes;

    const STATUS_NO = 1;
    const STATUS_INTRODUCE = 2;

    protected $table = 'course_users';

    protected $guarded = [];

    const STATUS_OPEN = 1;

    public function lessons()
    {
        return $this->hasMany(CourseLesson::class, 'course_id', 'course_id')
            ->orderBy('sort');
    }

    public function course()
    {
        return $this->hasOne(Course::class, 'id', 'course_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function collectLearnRecords()
    {
        return $this->hasOne(CollectLearnRecord::class, 'course_id', 'course_id');
    }

    public function bigCourses()
    {
        return $this->belongsToMany(BigCourse::class, 'big_course_course_pivot', 'course_id', 'big_course_id', 'id')
            ->withTimestamps();
    }

    public static function assign(Order $order)
    {
        if (! $order->isPaid()) {
            \Log::notice('尝试给未支付的订单分配课程权限：' . $order->trade_no);
            return false;
        }

        $user = $order->user;
        $courseUsers = $user->courseUsers;
        foreach ($order->goods as $good) {
            if ($good->goods_type == 'course') {
                $courseId = $good->goods_id;
                $courseUserExisted = $courseUsers->first(function ($item, $key) use ($courseId) {
                    return $item->course_id == $courseId;
                });
                if ($courseUserExisted) {
                    $courseUserExisted->order_id = $order->id;
                    $courseUserExisted->status = CourseUser::STATUS_OPEN;
                    $courseUserExisted->save();
                } else {
                    CourseUser::create([
                        'user_id' => $user->id,
                        'course_id' => $courseId,
                        'order_id' => $order->id,
                        'status' => CourseUser::STATUS_OPEN
                    ]);
                }
            }
        }

        return true;
    }
}
