<?php

namespace Modules\Examinee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Examination\Entities\ExaminationExaminee;
use Modules\ExaminationPaper\Entities\Question;

class ExamineeAnswerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $aFileMax = 52428800;
        if ($answer = $this->route('answer')) {
            $rules = [
                'question_option_id' => [
                    'integer',
                    Rule::exists('question_options', 'id')
                        ->where('question_id', $answer->question_id)
                ],
                'answer' => [
                    'required',
                    'string',
                ],
                'answer_file' => [
                    'file',
                    'mimes:zip',
                    'max:' . $aFileMax
                ],
                'answer_time' => [
                    'required',
                    'integer',
                ],
            ];
        } else {
            $rules = [
                'question_id' => [
                    'required',
                    'integer',
                    function($attribute, $value, $fail) {
                        $question = Question::select('questions.id')
                            ->leftjoin('major_problems', 'questions.major_problem_id', 'major_problems.id')
                            ->where('questions.id', $value)
                            ->where('examination_id', $this->examination_id)
                            ->first();
                            
                        if (empty($question)) {
                            return $fail('题目不存在或者不属于当前考试，请不要搞事情！');
                        }
                    },
                ],
                'examination_id' => [
                    'required',
                    'integer',
                    function($attribute, $value, $fail) {
                        $eexam = ExaminationExaminee::select('id', 'is_hand')
                            ->where('examination_id', $value)
                            ->where('examinee_id', $this->user()->id)
                            ->first();

                        if (empty($eexam)) {
                            return $fail('考生考试信息不存在，请不要瞎几把操作！');
                        }

                        if (! empty($eexam->is_hand)) {
                            return $fail('你已经交卷了，不可再答题！');
                        }
                    },
                ],
                'question_option_id' => [
                    'integer',
                    Rule::exists('question_options', 'id')
                        ->where('question_id', $this->question_id) // 是否验证？
                ],
                'answer' => [
                    'required',
                    'string',
                ],
                'answer_file' => [
                    'file',
                    'mimes:zip',
                    'max:' . $aFileMax
                ],
                'answer_time' => [
                    'required',
                    'integer',
                ],
                'type' => [
                    'required',
                    Rule::in(array_keys(Question::$categorys))
                ],
            ];
        }

        return $rules;
    }

    /**
     * Get rule messages.
     *
     * @return array
     * @author lizx
     */
    public function messages()
    {
        return [
            // ... 以上错误不适合反应给用户，前端过滤422错误提示就行！
            'answer_file.max' => '答案文件过大，请联系监考人员！'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
