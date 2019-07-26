<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Http\FormRequest;

class StoreUploadFile extends FormRequest
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
     */
    public function rules()
    {
        $max = config::get('filesystems.upload_max_size', 52428800);

        return [
            'file' => 'required|max:' . $max . '|file|mimes:jpeg,html,png,svg,gif,mpga,mp4,zip,txt,wav,doc,pdf,docx',
        ];
    }

    /**
     * Get the validation message that apply to the request.
     *
     * @return array
     * @author lzx
     */
    public function messages(): array
    {
        return [
            'file.required' => '没有上传文件或者上传错误',
            'file.max' => '文件上传超出服务器限制',
            'file.file' => '文件上传失败',
            'file.mimes' => '文件上传格式错误',
        ];
    }
}
