<?php

namespace Modules\ExaminationPaper\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\ExaminationPaper\Entities\Question;
use Illuminate\Validation\Rule;

class StoreMarkingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @author lizx
     */
    public function rules()
    {
        if (! empty($this->route('marking'))) {
            $rules = [
                'score' => 'required|integer',
            ];
        } else {
            $rules = [
                'examination_examinee_id' => 'required|exists:examination_examinees,id',
                'question_id' => [
                    'required',
                    'integer',
                    function ($attribute, $value, $fail) {
                        $question = Question::select('questions.id')
                            ->leftjoin('major_problems', 'questions.major_problem_id', 'major_problems.id')
                            ->leftjoin('examinations', 'major_problems.examination_id', 'examinations.id')
                            ->leftjoin('examination_examinees', 'examinations.id', 'examination_examinees.examination_id')
                            ->where('examination_examinees.id', $this->examination_examinee_id)
                            ->get();

                        if (! $question->pluck('id')->contains($value)) {
                            $fail('所传题目id不在本次考试！');
                        }
                    },
                ],
                'examination_answer_id' => [
                    'integer',
                    Rule::exists('examinee_answers', 'id')->where('question_id', $this->question_id) // todo 不够严格，暂时先这样
                ],
                'score' => 'required|integer',
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
            'examination_examinee_id.exists' => '考生考试信息不存在',
            'question_id.required' => '请传入题目',
            'score.required' => '请传入得分',
            'examination_answer_id.exists' => '解答不是当前题目的~'
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
