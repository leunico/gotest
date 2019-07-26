<?php

namespace Modules\Course\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourseLessonPost extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'course_id' => [
                'required',
                'integer',
                Rule::exists('courses', 'id')->whereNull('deleted_at')
            ],
            'title' => [
                'required',
                'string',
                'max:100',
                $this->route('lesson') ?
                Rule::unique('course_lessons')->where('course_id', $this->course_id)
                    ->whereNull('deleted_at')
                    ->ignore($this->route('lesson')->id) :
                Rule::unique('course_lessons')->where('course_id', $this->course_id)
                    ->whereNull('deleted_at')
            ],
            'cover_id' => 'required|integer|exists:files,id',
            'tutorial_link' => 'url|max:200',
            'lesson_intro' => 'string|max:250',
            'knowledge' => 'string|max:500',
            'materials' => 'string',
            'work' => 'string',
            'status' => 'required|integer|in:0,1',
            'is_drainage' => 'required|integer|in:0,1',
            'is_code' => 'integer|in:0,1',
            'count_user_learns' => 'integer',
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
            'cover_id.required' => '请添加封面',
            'cover_id.exists' => '封面文件不存在',
            'course_id.required' => '所属系列课必须填',
            'course_id.exists' => '系列课不存在',
            'title.required' => '标题必须传',
            'title.unique' => '标题已经存在了',
            'tutorial_link.url' => '不是一个链接',
            'tutorial_link.required' => '学习指南必须填',
            'lesson_intro.max' => '课时介绍太长了',
            'is_drainage.required' => '是否公开课需选择',
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
