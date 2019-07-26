<?php

declare(strict_types=1);

namespace Modules\Personal\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Course\Entities\Course;

class UserLearnRecordDetailRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'course_category' => [
                Rule::in(array_keys(Course::$courseMap)),
            ],
            'course_id' => [
                'integer', 'min:1',
            ],
            'lesson_id' => [
                'integer', 'min:1',
            ],
            'start_date' => [
                'date_format:Y-m-d',
            ],
            'end_date' => [
                'date_format:Y-m-d',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'course_category.in' => '输入非法',
            'course_id.integer' => '输入非法',
            'course_id.min' => '输入非法',
            'lesson_id.integer' => '输入非法',
            'lesson_id.min' => '输入非法',
            'start_date.date_format' => '输入非法',
            'end_date.date_format' => '输入非法',
        ];
    }
}
