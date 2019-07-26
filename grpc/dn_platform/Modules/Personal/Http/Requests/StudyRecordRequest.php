<?php
/**
 * Created by PhpStorm.
 * User: MRW
 * Date: 2018/11/8
 * Time: 16:53
 */

namespace Modules\Personal\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudyRecordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'problem_id' => 'required',
            'answer_id' => 'required',
            'section_id' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'problem_id.required' => '题目不合法',
            'answer_id.required' => '请选择答案',
            'section_id.in' => '参数错误',
        ];
    }
}