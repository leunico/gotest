<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Rules\ArrayExists;

class StoreUserRequest extends FormRequest
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
     */
    public function rules()
    {
        if ($this->routeIs('admin_user_update')) { // 后台编辑
            $user = $this->route('user');
            $rules = [
                'phone' => [
                    'required',
                    'cn_phone',
                    Rule::unique('users', 'phone')->ignore($user->id)
                ],
                'email' => [
                    // 'required',
                    'email',
                    Rule::unique('users', 'email')->ignore($user->id)
                ],
                'name' => [
                    'required',
                    'username',
                    'display_length:1,12',
                    Rule::unique('users', 'name')->ignore($user->id)
                ],
                'category' => [
                    'array',
                    function ($attribute, $value, $fail) {
                        foreach ($value as $value) {
                            if (! in_array($value, User::$categoryMap)) {
                                return $fail($attribute.' 用户类型不存在.');
                            }
                        }
                    }
                ],
                'role' => 'required|exists:roles,id',
                'real_name' => 'required|username|display_length:1,12',
                'password' => 'max:20|min:6',
                'sex' => Rule::in(array_keys(User::$sexMap)),
                'remarks' => 'max:500'
            ];
        } elseif ($this->routeIs('admin_user_store')) { // 后台添加
            $rules = [
                'phone' => [
                    'required',
                    'cn_phone',
                    Rule::unique('users', 'phone')
                ],
                'email' => [
                    'email',
                    Rule::unique('users', 'email')
                ],
                'name' => [
                    'required',
                    'username',
                    'display_length:1,12',
                    Rule::unique('users', 'name')
                ],
                'category' => [
                    'array',
                    function ($attribute, $value, $fail) {
                        foreach ($value as $value) {
                            if (! in_array($value, User::$categoryMap)) {
                                return $fail($attribute.' 用户类型不存在.');
                            }
                        }
                    }
                ],
                'role' => 'required|exists:roles,id',
                'password' => 'required|string|max:20|min:6',
                'real_name' => 'required|username|display_length:1,12',
                'remarks' => 'max:500'
            ];
        } else { // 个人更新
            $rules = [
                'phone' => [
                    'required',
                    'cn_phone',
                    Rule::unique('users', 'phone')->ignore($this->user()->id)
                ],
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users', 'email')->ignore($this->user()->id)
                ],
                'real_name' => 'required|username|display_length:1,12',
                'password' => 'max:20|min:6',
                'avatar' => 'required|url',
                'sex' => Rule::in(array_keys(User::$sexMap)),
                'remarks' => 'max:500'
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
            'phone.required' => '请输入用户手机号码',
            'phone.cn_phone' => '请输入大陆地区合法手机号码',
            'phone.unique' => '手机号码已经存在',
            'password.required' => '密码不能为空',
            'password.min' => '密码不能少于6位',
            'password.max' => '密码不能大于20位',
            'email.required'  => '请输入邮箱地址',
            'email.email'  => '请输入有效的邮箱地址',
            'email.unique' => '邮箱地址已存在',
            'name.required' => '请输入用户名',
            'real_name.required' => '请输入姓名',
            'real_name.username' => '姓名只能以非特殊字符和数字开头，不能包含特殊字符',
            'real_name.display_length' => '姓名长度不合法',
            'name.username' => '用户名只能以非特殊字符和数字开头，不能包含特殊字符',
            'name.display_length' => '用户名长度不合法',
            'name.unique' => '用户名已经被其他用户所使用',
            'remarks.max' => '备注不要超过450个字'
        ];
    }
}
