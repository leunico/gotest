<?php

namespace Modules\Course\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\ArrayExists;
use Modules\Course\Entities\Tag;

class StoreMusicPracticePost extends FormRequest
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
                $this->route('practice') ?
                Rule::unique('music_practices')->whereNull('deleted_at')->ignore($this->route('practice')->id) :
                Rule::unique('music_practices')->whereNull('deleted_at')
            ],
            'status' => 'required|in:0,1',
            'audio_link' => 'required|url',
            'book_id' => 'required|exists:files,id',
            'tags' => [
                'array',
                new ArrayExists(new Tag)
            ]
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
            'name.unique' => '这个名称的练耳已经存在了',
            'name.max' => '标题长度不能大于100',
            'book_id.required' => '缺少乐谱文件的上传信息',
            'book_id.exists' => '乐谱文件不存在',
            'audio_link.url' => '音频文件不是标准链接',
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
