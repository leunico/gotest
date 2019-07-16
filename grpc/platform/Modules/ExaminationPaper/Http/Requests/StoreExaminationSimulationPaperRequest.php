<?php

namespace Modules\ExaminationPaper\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExaminationSimulationPaperRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'examination_category_id' => 'required|exists:examination_categories,id',
            'content' => 'required|array'
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
