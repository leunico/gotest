<?php

namespace Modules\Course\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTagPost extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:100',
                $this->route('tag') ?
                Rule::unique('tags')->ignore($this->route('tag')->id) :
                Rule::unique('tags')
            ],
            'category' => 'integer|in:1',
            'cover_id' => 'integer|exists:files,id'
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
            'name.unique' => '这个名称的练耳已经存在了',
            'name.max' => '标题长度不能大于100',
            'category.in' => '类型不存在',
            'cover_id' => '封面图片必须传',
            'cover_id.exists' => '封面文件不存在',
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
