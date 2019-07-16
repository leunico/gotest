<?php

namespace Modules\Examinee\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use function GuzzleHttp\json_encode;

class ExamineeAnswer extends Model
{
    use SoftDeletes;

    public $fillable = [
        'examinee_id',
        'examination_id',
        'question_id'
    ];

    /**
     * 设定answer_file
     *
     * @param  array $value
     * @return void
     */
    public function setAnswerFileAttribute(array $value)
    {
        $this->attributes['answer_file'] = json_encode($value);
    }

    /**
     * 获取answer_file
     *
     * @param  int $value
     * @return array
     */
    public function getAnswerFileAttribute($value)
    {
        return json_decode($value, true);
    }
}
