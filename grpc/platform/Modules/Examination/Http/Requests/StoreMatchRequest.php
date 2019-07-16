<?php

namespace Modules\Examination\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @author lizx
     */
    public function rules()
    {
        $rules = [
            'title' => 'required|string',
            'start_at' => 'required|date',
            'end_at' => 'required|date',
            'cover_id' => 'required|integer|exists:files,id',
            'description' => 'required|string|max:500'
        ];

        return $rules;
    }

    /**
     * Get rule messages.
     *
     * @return array
     * @author lizx
     */
    public function messages()
    {
        return [
            'title.required' => '请输入标题',
            'start_at.required' => '请输入开始时间',
            'end_at.required' => '请输入结束时间',
            'cover_id.required' => '请上传封面',
            'cover_id.exists' => '封面文件不存在',
            'description.required' => '描述必须填写',
            'description.max' => '描述500字以内'
        ];
    }
}
