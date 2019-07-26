<?php

declare(strict_types = 1);

namespace Modules\Personal\Http\Requests;

use Illuminate\Validation\Rule;
use App\User;
use Illuminate\Foundation\Http\FormRequest;

class UserLearnRecordRequest extends FormRequest
{
    public function rules() : array
    {
        return [
            'start_grade' => [
                'integer',
            ],
            'end_grade' => [
                'integer',
            ],
            'sex' => [
                Rule::in(array_keys(User::$sexMap)),
            ],
            // 用户分类, 0: 非付费用户, 1: 付费用户
            'user_category' => [
                Rule::in([0, 1]),
            ],
            'start_date' => [
                'date_format:Y-m-d',
            ],
            'end_date' => [
                'date_format:Y-m-d',
            ],
            'keyword' => [
                'string', 'max:20',
            ],
        ];
    }

    public function messages() : array
    {
        return [
            'start_grade.integer' => '输入非法',
            'end_grade.integer' => '输入非法',
            'sex.in' => '输入非法',
            'user_category.in' => '输入非法',
            'start_date.date_format' => '输入非法',
            'end_date.date_format' => '输入非法',
            'keyword.string' => '输入非法',
            'keyword.max' => '内容过长',
        ];
    }
}
