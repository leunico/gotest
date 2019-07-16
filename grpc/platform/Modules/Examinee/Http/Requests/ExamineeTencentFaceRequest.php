<?php

namespace Modules\Examinee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Examinee\Entities\ExamineeTencentFace;

class ExamineeTencentFaceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'video' => 'required|is_base64',
            'type' => [
                'required',
                'integer',
                'in:' . implode(',', ExamineeTencentFace::$categorys),
                function($attribute, $value, $fail) {
                    $tfaces = ExamineeTencentFace::where('examination_examinee_id', $this->route('eexaminee')->id)
                        ->where($attribute, $value)
                        ->get();

                    if ($tfaces->count() > 4) {
                        return $fail('[Exceed]人脸核身请求已超过五次，请使用人工验证！');
                    }

                    if ($value == ExamineeTencentFace::TYPE_BEFORE && 
                        $tfaces->where('result', 'Success')->isNotEmpty()) {
                        return $fail('考前测试已通过了，您无需再请求！');
                    } elseif ($value == ExamineeTencentFace::TYPE_AFTER && 
                        $tfaces->where('result', 'Success')->count() > 2) {
                        // return $fail('考试人脸核身已成功验证三次，请停止反复登录！');
                    }
                }
            ]
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
