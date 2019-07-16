<?php

namespace Modules\ExaminationPaper\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Examination\Entities\ExaminationExaminee;

class MarkingRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'examination_examinee_id',
        'question_id',
        'examination_answer_id',
        'user_id',
        'score'
    ];

    public function examinationExaminee()
    {
        return $this->belongsTo(ExaminationExaminee::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
