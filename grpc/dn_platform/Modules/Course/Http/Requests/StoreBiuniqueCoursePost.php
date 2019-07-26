<?php

namespace Modules\Course\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Course\Entities\BiuniqueCourse;
use Illuminate\Validation\Rule;

class StoreBiuniqueCoursePost extends FormRequest
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
                'max:200',
                $this->route('course') ?
                Rule::unique('biunique_courses')->whereNull('deleted_at')->ignore($this->route('course')->id) :
                Rule::unique('biunique_courses')->whereNull('deleted_at')
            ],
            'category' => 'required|integer|in:' . implode(',', array_keys(BiuniqueCourse::$categoryMap)),
            'price_star' => 'required|numeric|min:0',
            'introduce' => 'required|string|max:500',
            'status' => 'required|in:0,1',
            'is_audition' => 'required|in:0,1',
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
            'introduce.max' => '课程介绍不超过500个字'
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
