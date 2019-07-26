<?php

namespace Modules\Educational\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class storeHolidayPost extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => 'required|date|unique:holidays',
            'name' => 'string|max:100',
            'describe' => 'string|max:190'
        ];
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
            'date.required' => '日期必须传',
            'date.date' => '不是日期格式',
            'date.unique' => '日期已经设置过了'
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
