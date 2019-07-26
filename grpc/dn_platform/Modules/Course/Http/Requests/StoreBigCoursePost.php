<?php

namespace Modules\Course\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ArrayExists;
use Illuminate\Validation\Rule;
use Modules\Course\Entities\Course;

class StoreBigCoursePost extends FormRequest
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
                $this->route('course') ?
                Rule::unique('big_courses')->whereNull('deleted_at')->ignore($this->route('course')->id) :
                Rule::unique('big_courses')->whereNull('deleted_at')
            ],
            'category' => 'required|in:1,2',
            'status' => 'required|in:0,1',
            'course_intro' => 'string|max:250',
            'cover_id' => 'integer|exists:files,id',
            'course_ids' => [
                'array',
                new ArrayExists(new Course, false, true)
            ]
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
            'course_intro.max' => '课程介绍不超过250个字',
            'cover_id.exists' => '封面文件不存在',
        ];
    }
}
