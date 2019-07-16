<?php

namespace Modules\Examinee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class ExamineePasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'old_password' => [
                'required',
                function($attribute, $value, $fail) {
                    if (! Hash::check($value, $this->user()->password)) {
                        return $fail('旧密码不正确');
                    }
                }
            ],
            'new_password' => 'required|string|max:20|min:6'
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
            'old_password.required' => '旧密码必须填写',
            'new_password.required' => '新密码必须填写',
            'new_password.max' => '新密码最长20位',
            'new_password.min' => '新密码最短6位'
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
