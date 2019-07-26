<?php

namespace Modules\Course\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\User;
use Modules\Course\Entities\Course;
use Modules\Personal\Entities\CourseUser;

class CoursePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * 后台设置观看权限
     *
     * @param \App\User $user
     * @return void
     */
    public function before($user)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
    }

    /**
     * 判断否有课程权限
     *
     * @param  \App\User  $user
     * @param  \Modules\Course\Entities\Course $course
     * @return bool
     * @author lizx
     */
    public function show(User $user, Course $course)
    {
        $user->courseUser = $course->courseUser()
            ->where('user_id', $user->id)
            ->where('status', CourseUser::STATUS_NO)
            ->first();

        return $user->can('api-course[' . $course->category . ']') ||
            $user->courseUser ||
            ($user->introduce && $user->userCourseLessons->contains('course_id', $course->id));
    }
}
