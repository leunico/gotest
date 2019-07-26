<?php

namespace Modules\Course\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Course\Entities\CourseSection;

class CourseSectionPolicy
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
     * 判断是否有环节权限
     *
     * @param  \App\User  $user
     * @param  \Modules\Course\Entities\CourseSection $section
     * @return bool
     * @author lizx
     */
    public function show(User $user, CourseSection $section)
    {
        return $user->can('api-course[' . $section->courseLesson->course->category . ']') || CourseUser::where('user_id', $user->id)
            ->where('course_id', $section->courseLesson->course_id)
            ->where('status', CourseUser::STATUS_NO)
            ->first();
    }
}
