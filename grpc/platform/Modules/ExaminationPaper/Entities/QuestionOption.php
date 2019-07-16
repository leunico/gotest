<?php

namespace Modules\ExaminationPaper\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Examinee\Entities\ExamineeQuestionSort;

class QuestionOption extends Model
{
    use SoftDeletes;

    public function esort()
    {
        return $this->morphOne(ExamineeQuestionSort::class, 'sorttable');
    }
}
