<?php

namespace Modules\Course\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProblemPost extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'category' => "required|in:1,2,3,4,5", // 1-单选题，2-判断题，3-多选题，4-操作题，5-问答题
            'course_category' => 'required|in:1,2',
            'preview_id' => 'integer|exists:files,id',
            'plan_duration' => 'integer',

            // options.
            'options' => 'array',

            // detail.
            'problem_text' => 'required|string',
            // 'answer' => 'string',
            // 'hint' => 'string',
        ];

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
            'problem_text.required' => '请输入题干内容',
            'course_category.in' => '请选择正确的课程体系',
            'preview_id.exists' => '预览文件不存在',
            'answer.exists' => '答案解析为字符串',
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
