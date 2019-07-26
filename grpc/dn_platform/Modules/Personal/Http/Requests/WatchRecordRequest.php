<?php
/**
 * Created by PhpStorm.
 * User: MRW
 * Date: 2018/11/10
 * Time: 15:09
 */

namespace Modules\Personal\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WatchRecordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'start_at' => 'required',
            'end_at' => 'required',
            'id' => 'required',
            'type' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'start_at.required' => '开始时间不对',
            'end_at.required' => '结束时间不对',
            'id.required' => '参数错误',
            'type.required' => '类型错误',
        ];
    }
}