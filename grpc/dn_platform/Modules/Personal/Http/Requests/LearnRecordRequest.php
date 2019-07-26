<?php
/**
 * Created by PhpStorm.
 * User: MRW
 * Date: 2018/11/10
 * Time: 11:05
 */

namespace Modules\Personal\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LearnRecordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required',
            'type' => 'required',
            'course_id' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => '参数错误',
            'type.required' => '类型错误',
            'course_id.required' => '参数错误',
        ];
    }
}