<?php

namespace Modules\Operate\Http\Requests;

use App\Rules\ArrayExists;
use App\User;
use Modules\Operate\Http\Requests\BaseRequest as FormRequest;
use Modules\Course\Entities\Course;
use Modules\Operate\Entities\Order;

class CourseOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $paymentMethod = Order::$paymentMethodMap;
        $user = User::find($this->user_id);
        return [
            'user_id' => 'required|exists:users,id',
            'category' => 'required|in:1,2',
            'course_ids' => [
                'required',
                'array',
                new ArrayExists(Course::where('category', $this->input('category'))),
                function ($attribute, $value, $fail) use ($user) {
                    if (!empty($user)) {
                        $courseIds = $user->courseUsers->pluck('course_id');
                        foreach ($value as $courseId) {
                            if ($courseIds->contains($courseId)) {
                                return $fail('课程[' . Course::find($courseId)->title . ']已经购买过了！');
                            }
                        }
                    }
                },
            ],
            'payment_method' => 'required|in:'. implode(',', array_keys($paymentMethod)),
            'real_price' => 'required|numeric|min:0',
            'paid_at' => 'required|date',
            'tx_num' => 'nullable|string',
            'youzan_trade_no' => 'string|max:60|required_if:payment_method,' . Order::PAYMENT_METHOD_YOUZAN,
            'good_remarks' => [
                // 'required',
                'array',
                function ($attribute, $value, $fail) {
                    foreach ($this->course_ids as $v) {
                        if (! array_key_exists($v, $value)) {
                            return $fail('商品名称为[' . Course::find($courseId)->title . ']的未设置商品备注');
                        }
                    }
                }
            ],
//            'is_paid' => 'required',
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

    public function messages()
    {
        return [
            'user_id.required' => '请选择用户',
            'user_id.exists' => '选择的用户不存在',
            'course_ids.required' => '课程必须填写',
            'payment_method.required' => '支付方式必须选择',
            'payment_method.in' => '你选择的支付方式不存在',
            'real_price.required' => '实际付款金额必须填写',
            'real_price.min' => '实际付款金额必须大于0',
            'real_price.numeric' => '实际付款金额必须是一个数字',
            'paid_at.required' => '付款时间必须填写',
            'paid_at.date' => '付款时间必须是一个日期格式',
            'youzan_trade_no.max' => '有赞订单号太长了',
            'youzan_trade_no.required_if' => '有赞订单必须要有有赞订单号',
        ];
    }
}
