<?php

namespace Modules\Personal\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvestmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->method() == 'POST') {
            return [
                'username' => 'required|string|username|unique:users,name|display_width:2,12',
                'name' => 'required|string|max:100',
                'password' => 'required|string|min:6',
                'mobile' => 'nullable|cn_phone|unique:users,phone',
                'remark' => 'nullable|max:2000',
            ];
        } else {
            return [
                'username' => [
                    'required',
                    'display_width:2,12',
                    'string','username',
                    Rule::unique('users', 'name')->ignore($this->route('investment')->user_id)
                    ],
                'name' => 'required|string|max:100',
                'password' => 'required|string|min:6',
                'mobile' => [
                    'nullable','cn_phone',
                    Rule::unique('users', 'phone')->ignore($this->route('investment')->user_id)],
                'remark' => 'nullable|max:2000',
            ];
        }

    }


    public function attributes()
    {
        return [
            'username' => '用户名',
            'name' => '机构名称',
            'password' => '机构密码',
            'mobile' => '手机号码',
            'remark' => '备注'
        ];
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
//    public function authorize()
//    {
//        return true;
//    }
}
