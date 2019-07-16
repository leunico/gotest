<?php

namespace Modules\ExaminationPaper\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Examinee\Entities\ExamineeAnswer;
use App\Traits\ExtendModel;
use App\Models\File;
use Modules\Examinee\Entities\ExamineeQuestionSort;

class Question extends Model
{
    use SoftDeletes, ExtendModel;

    const CATEGORY_SINGLE_ELECTION = 1;
    const CATEGORY_JUDGEMENT = 2;
    const CATEGORY_COMPLETION = 3;
    const CATEGORY_OPERATION = 4;
    const CATEGORY_MULTIPLE_TOPICS = 5;

    public $fillable = ['score'];

    public static $categorys = [
        self::CATEGORY_SINGLE_ELECTION => '单选题',
        self::CATEGORY_JUDGEMENT => '判断题',
        self::CATEGORY_COMPLETION => '填空题',
        self::CATEGORY_OPERATION => '操作题',
        self::CATEGORY_MULTIPLE_TOPICS => '多选题',
    ];

    public static $choiceQuestionCategory = [
        self::CATEGORY_SINGLE_ELECTION,
        self::CATEGORY_JUDGEMENT,
        self::CATEGORY_MULTIPLE_TOPICS,
    ];

    public static $choiceNotQuestionCategory = [
        self::CATEGORY_COMPLETION,
        self::CATEGORY_OPERATION,
    ];

    /**
     * 设定code_file
     *
     * @param  array $value
     * @return void
     */
    public function setCodeFileAttribute(array $value)
    {
        $this->attributes['code_file'] = json_encode($value);
    }

    /**
     * 获取code_file
     *
     * @param  int $value
     * @return array
     */
    public function getCodeFileAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * 设定Knowledge
     *
     * @param  array $value
     * @return void
     */
    public function setKnowledgeAttribute(array $value)
    {
        $this->attributes['knowledge'] = json_encode($value);
    }

    /**
     * 获取Knowledge
     *
     * @param  int $value
     * @return array
     */
    public function getKnowledgeAttribute($value)
    {
        return json_decode($value, true);
    }

    public function isChoiceQuestionCategory($category = null)
    {
        return in_array($category ?? $this->category, self::$choiceQuestionCategory);
    }

    public function isNotChoiceQuestionCategory($category = null)
    {
        return in_array($category ?? $this->category, self::$choiceNotQuestionCategory);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function majorProblem()
    {
        return $this->belongsTo(MajorProblem::class);
    }

    public function examineeAnswer()
    {
        return $this->hasMany(ExamineeAnswer::class);
    }

    public function markingRecord()
    {
        return $this->hasMany(MarkingRecord::class);
    }

    public function esort()
    {
        return $this->morphOne(ExamineeQuestionSort::class, 'sorttable');
    }
}
