<?php

namespace Modules\Course\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBiuniqueCourseLessonPost extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'biunique_course_id' => [
                'required',
                'integer',
                Rule::exists('biunique_courses', 'id')->whereNull('deleted_at')
            ],
            'title' => [
                'required',
                'string',
                'max:200',
                $this->route('lesson') ?
                Rule::unique('biunique_course_lessons')->where('biunique_course_id', $this->biunique_course_id)
                    ->whereNull('deleted_at')
                    ->ignore($this->route('lesson')->id) :
                Rule::unique('biunique_course_lessons')->where('biunique_course_id', $this->biunique_course_id)
                    ->whereNull('deleted_at')
            ],
            'introduce' => 'string|max:500',
            'status' => 'required|integer|in:0,1',
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
            'biunique_course_id.required' => '所属系列课必须填',
            'biunique_course_id.exists' => '系列课不存在',
            'title.required' => '标题必须传',
            'title.unique' => '标题已经存在了',
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
