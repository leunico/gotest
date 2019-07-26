<?php

declare(strict_types=1);

namespace Modules\Personal\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\User;
use Illuminate\Validation\Rule;

class StatisticsRequest extends FormRequest
{
    public function rules(): array
    {
        if ($this->routeIs('personal_statistics_user_all_detail')) {
            return [
                'start_date' => [
                    'required', 'date_format:Y-m-d',
                ],
                'end_date' => [
                    'required', 'date_format:Y-m-d',
                ],
                'grade' => [
                    'required', 'integer',
                ],
                'sex' => [
                    Rule::in(array_keys(User::$sexMap)),
                ],
                'keyword' => [
                    'string', 'max:20',
                ],
            ];
        }

        return [
            'start_date' => [
                'date_format:Y-m-d',
            ],
            'end_date' => [
                'date_format:Y-m-d',
            ],
            'start_grade' => [
                'integer',
            ],
            'end_grade' => [
                'integer',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.required' => '必填',
            'start_date.date_format' => '输入非法',
            'end_date.required' => '必填',
            'end_date.date_format' => '输入非法',
            'start_grade.integer' => '输入非法',
            'grade.required' => '必填',
            'grade.integer' => '输入非法',
            'end_grade.integer' => '输入非法',
            'sex.in' => '输入非法',
            'keyword.string' => '输入非法',
            'keyword.max' => '内容过长',
        ];
    }
}
