<?php

namespace Modules\ExaminationPaper\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\ExaminationPaper\Entities\Question;

class StoreQuestionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $maxFile = 52428800;
        $rules = [
            'category' => "required|in:" . implode(',', array_keys(Question::$categorys)), // 题目类型：1-单选题，2-判断题，3-填空题，4-操作题
            'score' => 'required|numeric',
            'level' => 'required|integer|max:10',
            'options' => 'array',
            'options.*.option_title' => 'required|string|max:500',
            'options.*.is_true' => 'required|in:0,1',
            'options.*.sort' => 'required|integer',
            'code_file' => [
                'file',
                'mimes:zip,txt,json',
                'max:' . $maxFile
            ],
            'question_title' => 'required|string',
            'completion_count' => 'integer',
            'sort' => 'required|integer',
            
            'answer' => 'max:1000',
            'knowledge' => 'array',
            // 'code' => 'string',
        ];

        if (empty($this->route('question'))) {
            $rules['major_problem_id'] = 'required|integer|exists:major_problems,id';
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
            'category.required' => '请选择题型',
            'category.in' => '请选择正确的题目类型',
            'question_title.required' => '请输入题干内容',
            'level.required' => '请选择难度系数',
            'major_problem_id.exists' => '大题不存在',
            'answer.max' => '答案太长了',
            'score.required' => '请填写分值'
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
