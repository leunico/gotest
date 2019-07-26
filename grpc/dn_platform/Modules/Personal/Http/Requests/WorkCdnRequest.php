<?php

namespace Modules\Personal\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkCdnRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => 'required|file',
            // 'image_cover' => 'required',
            'title' => 'required',
            'sb_url' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => '内容不能为空',
            'title.required' => '作品名称不能为空',
            'image_cover.required' => '请选择封面',
            'sb_url.required' => '素材路劲不正确',
        ];
    }
}
