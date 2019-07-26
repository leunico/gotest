<?php

namespace Modules\Course\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMusicTheoryPost extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:100',
                $this->route('music') ?
                Rule::unique('music_theories', 'name')->whereNull('deleted_at')->ignore($this->route('music')->id) :
                Rule::unique('music_theories', 'name')->whereNull('deleted_at')
            ],
            'status' => 'integer|in:0,1',
            'source_link' => 'required|url|max:255',
            'source_duration' => 'integer|min:0',
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
            'name.unique' => '这个名称的素材已经存在了',
            'name.max' => '名称长度不能大于100',
            'name.required' => '名称必须填写',
            'source_link.required' => '资源链接必须填',
            'source_link.url' => '不是一个标准的链接',
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
