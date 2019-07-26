<?php

declare(strict_types=1);

namespace Modules\Personal\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HomeworksRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file_url' => 'required',
            'type' => 'required|in:image,video',
            'lesson_id' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => '内容不能为空',
            'type.required' => '参数错误',
            'type.in' => '类型不正确',
            'lesson_id.required' => '参数错误'
        ];
    }
}
