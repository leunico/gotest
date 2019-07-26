<?php

declare(strict_types=1);

namespace Modules\Personal\Http\Requests;

use Illuminate\Validation\Rule;
use App\User;
use Illuminate\Foundation\Http\FormRequest;

class ClassLearnRecordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'start_date' => [
                'date_format:Y-m-d',
            ],
            'end_date' => [
                'date_format:Y-m-d',
            ],
            'keyword' => [
                'string', 'max:20',
            ],
            'teacher_id' => [
                'integer',
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.date_format' => '输入非法',
            'end_date.date_format' => '输入非法',
            'keyword.string' => '输入非法',
            'keyword.max' => '内容过长',
            'teacher_id.integer' => '老师id',
        ];
    }
}
