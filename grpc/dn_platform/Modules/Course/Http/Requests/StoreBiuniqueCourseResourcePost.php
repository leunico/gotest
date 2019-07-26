<?php

namespace Modules\Course\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBiuniqueCourseResourcePost extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @author lizx
     */
    public function rules()
    {
        $rules = [
            'title' => [
                'required',
                'string',
                'max:100',
                $this->route('resource') ?
                Rule::unique('biunique_course_resources')->ignore($this->route('resource')->id) :
                Rule::unique('biunique_course_resources')
            ],
            'category' => 'required|integer|in:1,2,3',
            'status' => 'required|in:0,1',
            'file_id' => 'required|integer|exists:files,id'
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
            'title.string' => '标题只能是字符串',
            'title.unique' => '标题已经存在了',
            'category.in' => '类别参数错误',
            'file_id.exists' => '资源文件不存在',
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
