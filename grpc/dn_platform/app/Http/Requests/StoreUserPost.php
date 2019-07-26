<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\User;
use App\Rules\VerifiableCode;

class StoreUserPost extends FormRequest
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
        if ($this->routeIs('user_update')) {
            $rules = [
                'phone' => [
                    // 'required',
                    'cn_phone',
                    Rule::unique('users', 'phone')->ignore($this->user()->id)
                ],
                'verifiable_code' => [
                    'required_with:phone',
                    'integer',
                    new VerifiableCode($this->phone)
                ],

                'name' => [
                    'required_with:phone',
                    'username',
                    'display_length:1,12',
                    Rule::unique('users', 'name')->ignore($this->user()->id), // todo 用户名是否唯一？
                ],
                'real_name' => 'required_with:phone|username|display_length:1,12',
                'avatar' => 'required_with:phone|url',
                'grade' => [
                    'required_with:phone',
                    Rule::in(array_keys(User::$gradeMap))
                ],
                'sex' => Rule::in(array_keys(User::$sexMap)),
                'province_id' => 'integer',
                'city_id' => 'integer',
                'district_id' => 'integer|exists:districts,code',
                'address_detail' => 'string|display_length:1,50',
                'receiver' => 'username|display_length:1,12',
            ];
        } elseif ($this->routeIs('login_wechat')) {
            $rules = [
                'phone' => [
                    'required',
                    'cn_phone',
                ],
                'verifiable_code' => [
                    // 'required_with:phone',
                    'integer',
                    new VerifiableCode($this->phone)
                ],
                'password' => 'required|string', // todo 密码不能为空
                'unionid' => 'required|string',
            ];
        } else {
            $rules = [
                'phone' => 'required_without:email|cn_phone|unique:users,phone',
                'email' => 'required_without:phone|email|max:128|unique:users,email',
                'password' => 'required|nullable|string',
                'name' => [
                    'required',
                    'username',
                    'display_length:2,12',
                    'unique:users,name',
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
            'phone.required_without' => '请输入用户手机号码',
            'phone.required' => '请输入用户手机号码',
            'phone.cn_phone' => '请输入大陆地区合法手机号码',
            'phone.unique' => '手机号码已经存在',
            'password.required' => '密码不能为空',
            'email.required_without' => '请输入用户邮箱地址',
            'email.email'  => '请输入有效的邮箱地址',
            'email.max'    => '输入的邮箱地址太长，应小于128字节',
            'email.unique' => '邮箱地址已存在',
            'name.required' => '请输入用户名',
            'real_name.required' => '请输入姓名',
            'real_name.username' => '姓名只能以非特殊字符和数字开头，不能包含特殊字符',
            'real_name.display_length' => '姓名长度不合法',
            'name.username' => '用户名只能以非特殊字符和数字开头，不能包含特殊字符',
            'name.display_length' => '用户名长度不合法',
            'name.unique' => '用户名已经被其他用户所使用',
            'verifiable_code.required_with' => '请输入验证码',
            'verifiable_code.required' => '请输入验证码',
            'avatar.required' => '请上传头像',
            'grade.required' => '请选择年级',
            'district_id.exists' => '地区参数不合法',
            'address_detail.display_length' => '详细地址长度不合法',
            'receiver.username' => '收件人只能以非特殊字符和数字开头，不能包含特殊字符',
            'receiver.display_length' => '收件人长度不合法',
        ];
    }
}
