<?php

namespace Modules\Examinee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExamineeDeviceProbingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'is_camera' => 'integer|in:0,1',
            'is_microphone' => 'integer|in:0,1',
            'is_chrome' => 'integer|in:0,1',
            'is_mc_ide' => 'integer|in:0,1',
            'is_scratch_ide' => 'integer|in:0,1',
            'is_python_ide' => 'integer|in:0,1',
            'is_c_ide' => 'integer|in:0,1',
        ];

        if (empty($this->route('deviceProbing'))) {
            $rules['examination_examinee_id'] = [
                'required',
                'integer',
                Rule::exists('examination_examinees', 'id')
                    ->where('examinee_id', $this->user()->id)
                    ->whereNull('deleted_at')
            ];
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
            // ... 以上错误不适合反应给用户，前端过滤422错误提示就行！
            'examination_examinee_id.exists' => '滴滴滴，考试不存在，或者不是你的考试！'
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
