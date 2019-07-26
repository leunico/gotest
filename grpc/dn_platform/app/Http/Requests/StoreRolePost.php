<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRolePost extends FormRequest
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
        return [
            'name' => [
                'required',
                'string',
                $this->route('role') ?
                Rule::unique('roles')->ignore($this->route('role')->id) :
                Rule::unique('roles')
            ],
            'title' => 'required|string',
            'description' => 'string|max:255',
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
            'name.required' => '请输入角色名称',
            'name.unique' => '角色名称已经存在了',
            'title.required' => '标题还是要传的啊'
        ];
    }
}
