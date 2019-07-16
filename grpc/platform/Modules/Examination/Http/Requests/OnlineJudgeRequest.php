<?php

namespace Modules\Examination\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Solution;

class OnlineJudgeRequest extends FormRequest
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
            // 'question_id' => 'required|exists:questions,id', // todo 这里要加验证吗、？？（不了）
            'language' => 'required|in:' . implode(',', array_keys(Solution::$languages)),
            'source' => 'required|string|max:65535',
            'input_text' => 'string'
        ];

        return $rules;
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
