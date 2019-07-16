<?php

namespace Modules\Examination\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Examinee\Entities\ExamineeOperation;

class ExamineeOperationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @author lizx
     */
    public function rules()
    {
        $rules = [
            'category' => 'required|in:' . implode(',', ExamineeOperation::$categorys),
            'remark' => 'string',
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
            // ...
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
