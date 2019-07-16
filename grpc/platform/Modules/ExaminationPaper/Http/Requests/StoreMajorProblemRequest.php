<?php

namespace Modules\ExaminationPaper\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\ExaminationPaper\Entities\Question;

class StoreMajorProblemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @author lizx
     */
    public function rules()
    {
        $rules = [
            'title' => 'required|string',
            'sort' => 'required|integer',
            'category' => "required|in:" . implode(',', array_keys(Question::$categorys)),
            'description' => 'required|string|max:500',
            'is_question_disorder' => 'required|in:0,1',
            'is_option_disorder' => 'integer|in:0,1'
            // 'examination_id' => 'required|integer|exists:examinations,id'

        ];

        if (empty($this->route('problem'))) {
            $rules['examination_id'] = 'required|integer|exists:examinations,id';
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
            'title.required' => '请输入标题',
            'sort.required' => '请传入排序',
            'examination_id.required' => '请传入关联考试',
            'examination_id.exists' => '考试不存在',
            'description.required' => '描述必须填写',
            'description.max' => '描述500字以内'
        ];
    }
}
