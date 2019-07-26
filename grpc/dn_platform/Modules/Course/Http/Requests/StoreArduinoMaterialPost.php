<?php

namespace Modules\Course\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreArduinoMaterialPost extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $max = 2 * 10480;  # 暂定20M的限制（放到config）
        $rules = [
            'name' => [
                'required',
                'string',
                'max:100'
            ],
            'is_arduino' => 'required|in:1,2',
            'source_link' => 'required|url',
            'file' => [
                'max:' . $max,
                'file',
                'mimes:jpeg,bmp,png,gif,html,svg'
            ]
        ];

        $rule_name = Rule::unique('arduino_materials')->whereNull('deleted_at');
        if (empty($this->route('arduino'))) {
            $rules['file'][] = 'required';
            $rules['name'][] = $rule_name;
        } else {
            $rules['name'][] = $rule_name->ignore($this->route('arduino')->id);
        }

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
            'name.unique' => '这个名称的素材已经存在了',
            'name.max' => '标题长度不能大于100',
            'file.required' => '缺少文件的上传信息',
            'file.max' => '文件最大20M',
            'category.in' => '类别参数错误',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
