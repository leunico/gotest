<?php

namespace Modules\ExaminationPaper\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ExtendModel;

class MajorProblem extends Model
{
    use SoftDeletes, ExtendModel;

    public function questions()
    {
        return $this->hasMany(Question::Class);
    }
}
