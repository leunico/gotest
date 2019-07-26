<?php

declare(strict_types=1);

namespace App\Traits\Relations;

use Modules\Educational\Entities\StudyClass;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Educational\Entities\Teacher;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Course\Entities\CourseLesson;
use Modules\Educational\Entities\ClassCourseLessonUnlocak;
use Modules\Personal\Entities\CourseUser;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasClasses
{
    /**
     * class - my class.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * @author lizx
     */
    public function class(): BelongsToMany
    {
        return $this
            ->belongsToMany(StudyClass::class, 'class_students', 'user_id', 'class_id')
            ->where('classes.status', StudyClass::STATUS_ON)
            ->withPivot('id')
            ->withTimestamps();
    }

    /**
     * class - all class.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * @author lizx
     */
    public function allClass(): BelongsToMany
    {
        return $this
            ->belongsToMany(StudyClass::class, 'class_students', 'user_id', 'class_id')
            ->withPivot('id')
            ->withTimestamps();
    }

    /**
     * teacher - all class.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author lizx
     */
    public function teacherClass(): HasMany
    {
        return $this->hasMany(StudyClass::class, 'teacher_id');
    }

    /**
     * class by course.
     *
     * @param int $course_id
     * @return \Modules\Educational\Entities\StudyClass
     * @author lizx
     */
    public function getClass(int $course_id): ?StudyClass
    {
        $courseUser = CourseUser::where('user_id', $this->id)
            ->where('course_id', $course_id)
            ->select('id', 'class_id')
            ->first();

        return empty($courseUser->class_id) ? null : StudyClass::find($courseUser->class_id)
            ->load(['teacher', 'teacher.teacher', 'teacher.teacher.qrcodeFile']);
    }
}
