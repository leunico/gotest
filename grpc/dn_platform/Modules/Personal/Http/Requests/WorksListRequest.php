<?php

declare(strict_types=1);

namespace Modules\Personal\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class WorksListRequest extends FormRequest
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
            'status' => [
                Rule::in([0, 1]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.date_format' => '输入非法',
            'end_date.date_format' => '输入非法',
            'status.in' => '输入非法',
        ];
    }
}
