<?php

declare(strict_types=1);

namespace Modules\Personal\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Operate\Entities\Order;

class OrderListRequest extends FormRequest
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
            'status' => [
                Rule::in(array_keys(Order::$payStatusMap)),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.date_format' => '输入非法',
            'end_date.date_format' => '输入非法',
            'keyword.string' => '输入非法',
            'keyword.max' => '内容过长',
        ];
    }
}
