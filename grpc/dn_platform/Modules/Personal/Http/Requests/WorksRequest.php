<?php

namespace Modules\Personal\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorksRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => 'required',
            'image_cover' => 'required',
            'title' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => '内容不能为空',
            'title.required' => '作品名称不能为空',
            'image_cover.required' => '请选择封面',
        ];
    }
}
