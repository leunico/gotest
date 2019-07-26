<?php

namespace Modules\Educational\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Educational\Entities\Teacher;

class UpdateTeacherPut extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     *  @author lizx
     */
    public function rules()
    {
        return [
            // // todo 二进制多选
            // 'authority' => [
            //     'array',
            //     function ($attribute, $value, $fail) {
            //         $authoritys = array_keys(Teacher::$authoritys);
            //         if (! empty(array_diff($value, $authoritys))) {
            //             return $fail($attribute.' 有不存在的[权限].');
            //         }
            //     }
            // ],
            'type' => 'integer|in:' . implode(',', array_keys(Teacher::$typeMap)),
            'qrcode' => 'integer|exists:files,id'
        ];
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
            'qrcode.exists' => '二维码文件不存在'
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
