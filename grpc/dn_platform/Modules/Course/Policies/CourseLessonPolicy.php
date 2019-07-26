<?php

namespace Modules\Course\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\User;
use Modules\Course\Entities\CourseLesson;
use Modules\Personal\Entities\CourseUser;
use Illuminate\Support\Carbon;
use Modules\Educational\Entities\ClassCourseLessonUnlocak;

class CourseLessonPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        // ...
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
     * 判断是否有主题权限
     *
     * @param  \App\User  $user
     * @param  \Modules\Course\Entities\CourseLesson  $lesson
     * @return bool
     * @author lizx
     */
    public function show(User $user, CourseLesson $lesson)
    {
        if ($user->can('api-course[' . $lesson->course->category . ']')
            or $user->can('course-unlock-no-limit')) {
            return true;
        }

        $courseUser = CourseUser::where('user_id', $user->id)
            ->where('course_id', $lesson->course_id)
            ->where('status', CourseUser::STATUS_NO)
            ->first();

        if (! $courseUser && $user->introduce && $user->userCourseLessons->contains('course_id', $lesson->course_id)) {
            return true;
        }

        if ($lesson->isNotDrainage() || ($lesson->course && $lesson->course->isNotDrainage())) {
            return true;
        }

        if ($courseUser && ! empty($courseUser->class_id)) {
            $unlock = ClassCourseLessonUnlocak::where('course_lesson_id', $lesson->id)
                ->where('class_id', $courseUser->class_id)
                ->first();

            return $unlock ? Carbon::now()->gte(Carbon::parse($unlock->unlock_day)) : false;
        }

        return false;
    }
}
