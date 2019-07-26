<?php

declare(strict_types=1);

namespace Modules\Personal\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Course\Entities\Course;

class CourseLessonLearnRecordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'category' => [
                Rule::in([1, 2]),
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
            'keyword.max' => '内容过长',
        ];
    }
}
