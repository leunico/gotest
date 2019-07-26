<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePermissionPost extends FormRequest
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
                $this->route('permission') ?
                Rule::unique('permissions')->ignore($this->route('permission')->id) :
                Rule::unique('permissions')
            ],
            'title' => 'required|string',
            'category' => 'required|string|max:50',
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
            'name.unique' => '名称已经存在了',
            'title.required' => '标题还是要传的啊',
            'category.required' => '分类还是要传的啊'
        ];
    }
}
