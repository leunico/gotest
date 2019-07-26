<?php

namespace Modules\Course\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Course\Entities\Course;

class StoreCoursePost extends FormRequest
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
                Rule::unique('courses')->whereNull('deleted_at')->ignore($this->route('course')->id) :
                Rule::unique('courses')->whereNull('deleted_at')
            ],
            'category' => 'required|integer|in:' . implode(',', array_keys(Course::$courseMap)),
            'type' => 'required|integer|in:' . implode(',', array_keys(Course::$courseTypeMap)),
            'price' => 'numeric|min:0',
            'original_price' => 'numeric|min:0',
            'learn_duration' => 'numeric|min:0',
            'level' => [
                'required',
                'in:0,1,2,3,4,5,6,7,8',
            ],
            'status' => 'required|in:0,1',
            'course_intro' => 'string|max:250',
            'is_mail' => 'required|in:0,1',
            'is_drainage' => 'required|in:0,1',
            'cover_id' => 'integer', // exists:files,id // todo 前端老传0
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
            'type.in' => '体系参数错误',
            'level.in' => '课程等级错误',
            'course_intro.max' => '课程介绍不超过250个字',
            'level.unique' => '必修课的一个课程体系的一个等级只能上架一个课程',
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
