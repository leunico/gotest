<?php

declare(strict_types=1);

namespace Modules\Personal\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CourseListRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'category' => [
                Rule::in([1, 2]),
            ],
            'course_id' => [
                'integer', Rule::exists('courses', 'id'),
            ],
            'keyword' => [
                'string', 'max:20',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'category.in' => '输入非法',
            'course_id.integer' => '输入非法',
            'course_id.exists' => '课程不存在',
            'keyword.string' => '输入非法',
        ];
    }
}
