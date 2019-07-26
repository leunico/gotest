<?php

namespace Modules\Course\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\File;
use Modules\Personal\Entities\SubjectSubmission;

class Problem extends Model
{
    use SoftDeletes;

    protected $fillable = [];

    public static $choice_question_category = [1, 2, 3];

    public static $categorys = [
        '单选题' => 1,
        '判断题' => 2,
        '多选题' => 3,
        '操作题' => 4,
        '问答题' => 5,
    ];

    public function isChoiceQuestionCategory($category = null)
    {
        return in_array($category ?? $this->category, self::$choice_question_category);
    }

    public function options()
    {
        return $this->hasMany(ProblemOption::class);
    }

    public function detail()
    {
        return $this->hasOne(ProblemDetail::class);
    }

    public function preview()
    {
        return $this->belongsTo(File::class, 'preview_id')
            ->select('id', 'driver_baseurl', 'origin_filename', 'filename', 'mime');
    }

    public function sectionPivots()
    {
        return $this->hasMany(CourseSectionProblemPivot::class);
    }

    public function subjectSubmission()
    {
        return $this->hasMany(SubjectSubmission::class);
    }
}
