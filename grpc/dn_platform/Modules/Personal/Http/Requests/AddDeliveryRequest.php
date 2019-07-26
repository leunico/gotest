<?php

namespace Modules\Personal\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddDeliveryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'receiver' =>'required',
            'province_id' =>'required',
            'city_id' =>'required',
            'district_id' =>'required',
            'detail_address' =>'required',
            'category' => 'required',
            'express_company' => 'required',
            'track_number' => 'required',
            'send_at' => 'required',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function messages(): array
    {
        return [
            'receiver.required' => '收件人不能为空',
            'province_id.required' => '省份不能为空',
            'city_id.required' => '城市不能为空',
            'district_id.required' => '区域不能为空',
            'detail_address.required' => '详细地址不能为空',
            'category.required' => '请选择寄件类型',
            'express_company.required' => '请填写快递公司',
            'track_number.required' => '请填写快递单号',
            'send_at.required' => '寄件日期不能为空',
        ];
    }
}
