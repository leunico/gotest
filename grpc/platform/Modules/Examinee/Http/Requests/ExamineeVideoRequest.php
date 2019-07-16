<?php

namespace Modules\Examinee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Examinee\Entities\ExamineeVideo;

class ExamineeVideoRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'video_url' => 'required|url',
            'type' => 'required|integer|in:' . implode(',', ExamineeVideo::$types)
            // 'examination_examinee_id' => [
            //     'required',
            //     'integer',
            //     Rule::exists('examination_examinees', 'id')
            //         ->where('examinee_id', $this->user()->id)
            // ],
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
            // ... 以上错误不适合反应给用户，前端过滤422错误提示就行！
            'examination_examinee_id.exists' => '考生考试不存在！',
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
