<?php

namespace Modules\Educational\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Educational\Entities\StudyClass;
use App\User;
use Modules\Course\Entities\Course;

class StoreClassPost extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     *  @author lizx
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                $this->route('class') ?
                Rule::unique('classes')->whereNull('deleted_at')->ignore($this->route('class')->id) :
                Rule::unique('classes')->whereNull('deleted_at')
            ],
            'category' => [
                'required',
                'integer',
                'in:' . implode(',', array_keys(StudyClass::$categorys))
            ],
            'course_category' => [
                'required',
                'integer',
                'in:' . implode(',', array_keys(Course::$courseMap))
            ],
            'pattern' => [
                'required',
                'integer',
                'in:' . implode(',', array_keys(StudyClass::$patterns))
            ],
            'frequency' => [
                'required',
                'integer',
                'in:' . implode(',', array_keys(StudyClass::$frequencies))
            ],
            'teacher_id' => [
                // 'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $teacher = User::find($value);
                    if (! $teacher || ! $teacher->hasRole('course_teacher')) {
                        return $fail($attribute.' 是不存在的老师或者用户不是老师');
                    }
                }
            ],
            'entry_at' => 'required|date',
            'unlock_at' => 'required|date',
            'leave_at' => 'date',
            'unlocak_times' => 'required|array',
            'big_course_id' => 'integer|exists:big_courses,id', // |required_if:category,' . StudyClass::CATEGORY_BIG_COURSE
            'course_id' => 'integer|exists:courses,id', // |required_if:category,' . StudyClass::CATEGORY_COURSE
            'status' =>  [
                'required',
                'integer',
                'in:0,1'
            ],
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
            'name.required' => '班级名称需填写',
            'name.unique' => '班级名称已存在',
            'category.required' => '类型需选择',
            'course_category.required' => '课程类型需选择',
            'pattern.required' => '解锁模式需选择',
            'frequency.required' => '频率需选择',
            'entry_at.required' => '开课日期必须填写',
            'unlock_at.required' => '开始解锁时间必须填写',
            'unlocak_times.required' => '解锁时间必须选择',
            'course_id.exists' => '系列课不存在',
            'big_course_id.exists' => '年微课不存在',
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
