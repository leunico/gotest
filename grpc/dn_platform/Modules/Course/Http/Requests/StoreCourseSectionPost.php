<?php

namespace Modules\Course\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourseSectionPost extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'title' => 'required|string|max:100',
            'course_lesson_id' => [
                'required',
                'integer',
                Rule::exists('course_lessons', 'id')->whereNull('deleted_at')
            ],
            'category' => [
                'required',
                'in:1,2' // 1-视频，2-文档
            ],
            'status' => 'required|in:0,1',
            'source_link' => 'url|max:255',
            'source_duration' => 'required_with:source_link|integer|min:0',
            'section_intro' => 'string',
            'arduino_material_id' => [
                'integer',
                Rule::exists('arduino_materials', 'id')->whereNull('deleted_at')
            ],
            'problems' => 'array'
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
            'title.required' => '标题必须传',
            'title.max' => '标题太长了',
            'course_lesson_id.exists' => '课程主题不存在',
            'arduino_material_id.exists' => 'arduino素材不存在',
            'category.in' => '类别参数错误',
            'source_link.url' => '不是一个标准的链接',
            'source_duration.required_with' => '有资源必须上传资源的时长',
            'course_lesson_id.required' => '课程主题必须选',
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
