<?php

declare(strict_types=1);

namespace App\Traits\Relations;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Personal\Entities\CourseUser;
use Modules\Operate\Entities\StarPackageUser;
use Modules\Course\Entities\Course;
use Modules\Personal\Entities\UserCourseLesson;
use Modules\Course\Entities\CourseLesson;

trait HasModelUser
{
    /**
     * 用户课程权限
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     * @author lizx
     */
    public function courseUsers(): HasMany
    {
        return $this->hasMany(CourseUser::class, 'user_id')
            ->select('user_id', 'course_id', 'order_id', 'memo', 'created_at', 'class_id')
            ->where('status', CourseUser::STATUS_NO);
    }

    /**
     * 用户转介绍权限
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     * @author lizx
     */
    public function userCourseLessons(): HasMany
    {
        return $this->hasMany(UserCourseLesson::class, 'user_id')
            ->select('user_id', 'course_id', 'course_lesson_id', 'type');
    }

    /**
     * 用户星星包权限
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     * @author lizx
     */
    public function starPackgeUsers(): HasMany
    {
        return $this->hasMany(StarPackageUser::class, 'user_id')
            ->select('user_id', 'star_package_id', 'order_id', 'memo', 'created_at', 'star')
            ->where('status', StarPackageUser::STATUS_NO);
    }

    /**
     * 用户课程s
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * @author lizx
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_users')
            ->wherePivot('course_users.status', CourseUser::STATUS_NO)
            ->withTimestamps();
    }

    /**
     * 用户转介绍课屎权限
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * @author lizx
     */
    public function introduceCourseLessons(): BelongsToMany
    {
        return $this->belongsToMany(CourseLesson::class, 'user_course_lessons')
            ->withPivot('course_lesson_id', 'course_id', 'type')
            ->withTimestamps();
    }
}
