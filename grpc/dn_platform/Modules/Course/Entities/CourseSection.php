<?php

namespace Modules\Course\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Course\Entities\Concerns\ModelExtend;
use Modules\Course\Entities\Concerns\PivotTrait;
use Modules\Personal\Entities\SubjectSubmission;
use Modules\Personal\Entities\LearnRecord;
use Modules\Personal\Entities\LearnProgress;

class CourseSection extends Model
{
    use SoftDeletes, ModelExtend, PivotTrait; //todo 原始用法[PivotEventTrait]，为了兼容扩展laravel/telescope。。。MMP

    const SECTION_STATUS_ON = 1;

    protected $fillable = [];

    public static $problem_category = [1, 3]; // 需要插入题目的类型

    public static function boot()
    {
        parent::boot();

        static::pivotAttached(function ($model, $relationName, $pivotIds) {
            if ($relationName == 'problems') {
                Problem::whereIn('id', $pivotIds)->increment('use_count');
            }
        });

        static::pivotDetached(function ($model, $relationName, $pivotIds) {
            if ($relationName == 'problems') {
                Problem::whereIn('id', $pivotIds)->decrement('use_count');
            }
        });
    }

    public function isSectionProblem($category = null)
    {
        return in_array($category ?? $this->category, self::$problem_category);
    }

    public function courseLesson()
    {
        return $this->belongsTo(CourseLesson::class);
    }

    public function problems()
    {
        return $this->belongsToMany(Problem::class, 'course_section_problem_pivot')
            ->withTimestamps();
    }

    public function actionStatus()
    {
        return empty($this->status) ? $this->increment('status') : $this->decrement('status');
    }

    public function arduinoMaterial()
    {
        return $this->belongsTo(ArduinoMaterial::class);
    }

    public function subjects()
    {
        return $this->hasMany(SubjectSubmission::class, 'section_id');
    }

    public function records()
    {
        return $this->hasMany(LearnRecord::class, 'section_id');
    }

    public function learnProgresses()
    {
        return $this->hasMany(LearnProgress::class, 'section_id');
    }

    public function learnRecords()
    {
        return $this->hasMany(LearnRecord::class, 'section_id');
    }

    public function sectionPivots()
    {
        return $this->hasMany(CourseSectionProblemPivot::class);
    }
}
