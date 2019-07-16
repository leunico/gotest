<?php

namespace Modules\Examinee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExamineeTechnicalSupportRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'description' => 'required|string|max:500',
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
            'description.required' => '需要写点描述哦~',
            'description.max' => '描述请控制在500字以内~',
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
