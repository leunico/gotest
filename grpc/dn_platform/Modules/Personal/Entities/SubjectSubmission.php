<?php
/**
 * Created by PhpStorm.
 * User: MRW
 * Date: 2018/11/8
 * Time: 16:18
 */

namespace Modules\Personal\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Course\Entities\Problem;
use Modules\Course\Entities\ProblemDetail;

class SubjectSubmission extends Model
{
    protected $table = 'subject_submissions';

    protected $guarded = [];

    public function problem_details()
    {
        return $this->belongsTo(ProblemDetail::class, 'problem_id','problem_id')
            ->select('id','problem_id','problem_text');
    }

    public function problems()
    {
        return $this->belongsTo(Problem::class, 'problem_id','id');
    }

}