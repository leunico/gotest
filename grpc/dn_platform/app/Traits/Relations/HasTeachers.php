<?php

declare(strict_types=1);

namespace App\Traits\Relations;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Educational\Entities\Teacher;
use Modules\Educational\Entities\TeacherOfficeTime;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Educational\Entities\TeacherSort;
use Modules\Course\Entities\BiuniqueCourse;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Educational\Entities\TeacherCourse;

trait HasTeachers
{
    /**
     * is teacher.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author lizx
     */
    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    /**
     * is teacher office times
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     * @author lizx
     */
    public function teacherOfficeTimes(): HasMany
    {
        return $this->hasMany(TeacherOfficeTime::class);
    }

    /**
     * is teacher sorts
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     * @author lizx
     */
    public function teacherSorts(): HasMany
    {
        return $this->hasMany(TeacherSort::class);
    }

    /**
     * is teacher sort
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author lizx
     */
    public function teacherSort(): HasOne
    {
        return $this->hasOne(TeacherSort::class);
    }

    /**
     * teacher - formal courses
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * @author lizx
     */
    public function teacherFormalCourses(): BelongsToMany
    {
        return $this->belongsToMany(BiuniqueCourse::class, 'teacher_courses')
            ->wherePivot('type', TeacherCourse::TYPE_ZS)
            ->where('biunique_courses.status', BiuniqueCourse::STATUS_ON);
    }

    /**
     * teacher - audition courses
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * @author lizx
     */
    public function teacherAuditionCourses(): BelongsToMany
    {
        return $this->belongsToMany(BiuniqueCourse::class, 'teacher_courses')
            ->wherePivot('type', TeacherCourse::TYPE_ST)
            ->where('biunique_courses.status', BiuniqueCourse::STATUS_ON);
    }

    /**
     * teacher - all courses
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * @author lizx
     */
    public function teacherCourses(): BelongsToMany
    {
        return $this->belongsToMany(BiuniqueCourse::class, 'teacher_courses')
            ->withPivot('type')
            ->where('biunique_courses.status', BiuniqueCourse::STATUS_ON);
    }
}
