<?php

namespace Modules\Operate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required',
            'keywords' => 'required',
            'description' => 'required',
            'abstract' => 'required|string|max:2000',
            'body' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => '文章标题不能为空',
            'keywords.required' => '关键字不能为空',
            'description.required' => '描述不能为空',
            'body.required' => '文章内容不能为空',
            'abstract.required' => '简介必须填写'
        ];
    }
}
