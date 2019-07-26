<?php

namespace Modules\Course\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStarPackagePost extends FormRequest
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
            'title' => [
                'required',
                'string',
                'max:100',
                $this->route('star') ?
                Rule::unique('star_packages')->whereNull('deleted_at')->ignore($this->route('star')->id) :
                Rule::unique('star_packages')->whereNull('deleted_at')
            ],
            'count_lesson' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'star' => 'required|integer|min:0',
            'status' => 'required|in:0,1',
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
            'title.string' => '标题只能是字符串',
            'title.unique' => '标题已经存在了'
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
