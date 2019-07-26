<?php

namespace Modules\Operate\Http\Requests;

use Modules\Operate\Http\Requests\BaseRequest as FormRequest;

class BannerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'file_id' => 'required',
            'number' => 'required',
            'category' => 'required',
            'platform' => 'required',
            'status' => 'required',
            'type' => 'required',
            'belong_page' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'file_id.required' => '请先上传文件',
            'number.required' => '编号不能为空',
            'category.required' => '请选择类型',
            'platform.required' => '平台不能为空',
            'status.required' => '请选择是否有效',
            'type.required' => '参数错误',
        ];
    }
}
