<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\User;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        if ($this->routeIs('*user_manage_set_userinfo')) {
            return [
                'name' => [
                    'display_width:2,12', 'username', Rule::unique('users', 'name')->ignore($this->route('user')->name, 'name'),
                ],
                'phone' => [
                    'cn_phone', Rule::unique('users', 'phone')->ignore($this->route('user')->phone, 'phone'),
                ],
                'password' => [
                    'string', 'confirmed',
                ],
                'real_name' => [
                    'display_width:2,10',
                ],
                'grade' => [
                    'integer',
                ],
                'avatar' => [
                    'url',
                ],
                'sex' => [
                    Rule::in(array_keys(User::$sexMap)),
                ],
                'account_status' => [
                    Rule::in([0, 1]),
                ],
            ];
        }

        return [
            'name' => [
                'required', 'display_width:2,12', 'username', Rule::unique('users', 'name'),
            ],
            'phone' => [
                'required', 'cn_phone', Rule::unique('users', 'phone'),
            ],
            'password' => [
                'required', 'string', 'confirmed',
            ],
            'real_name' => [
                'required', 'display_width:2,10',
            ],
            'grade' => [
                'integer',
            ],
            'avatar' => [
                'url',
            ],
            'sex' => [
                Rule::in(array_keys(User::$sexMap)),
            ],
            'account_status' => [
                Rule::in([0, 1]),
            ],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => '用户名必填',
            'name.unique' => '用户名已存在',
            'name.display_length' => '用户名2-12个字符',
            'name.username' => '用户名输入非法',
            'phone.required' => '手机号码必填',
            'phone.cn_phone' => '手机号码输入非法',
            'phone.unique' => '手机号码已存在',
            'password.required' => '密码必填',
            'password.string' => '密码输入非法',
            'password.confirmed' => '确认密码错误',
            'real_name.required' => '真实姓名必填',
            'real_name.display_length' => '真实姓名2-10个字符',
            'avatar.required' => '头像必填',
            'avatar.url' => '头像输入非法',
            'grade.integer' => '年级输入非法',
            'sex.in' => '性别输入非法',
            'account_status.in' => '状态输入非法',
        ];
    }
}
