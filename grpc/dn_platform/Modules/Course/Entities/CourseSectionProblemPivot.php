<?php

namespace Modules\Course\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Personal\Entities\SubjectSubmission;

class CourseSectionProblemPivot extends Model
{
    protected $table = 'course_section_problem_pivot';

    protected $fillable = [];

    public function detail()
    {
        return $this->hasOne(ProblemDetail::class,'problem_id','problem_id');
    }

    public function problem()
    {
        return $this->hasOne(Problem::class,'id','problem_id');
    }

}
