<?php

namespace Modules\Operate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\VerifiableCode;
use App\Rules\ArrayExists;
use Modules\Course\Entities\Course;
use Illuminate\Validation\Rule;
use Modules\Operate\Entities\Order;
use App\User;

class StoreOrderPost extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = $this->user();
        $categoryMap = Order::$categoryMap;
        $rules = [
            'pay_type' => 'required|integer|in:0,1', //是否第一次购买：0-否，1-是【是否有手机号判断】
            'phone' => [
                'required_if:pay_type,1',
                'cn_phone',
                $user ? Rule::unique('users', 'phone')->ignore($user->id) : Rule::unique('users', 'phone')
            ],
            'verifiable_code' => [
                'required_if:pay_type,1',
                'integer',
                new VerifiableCode($this->phone)
            ],

            // 选填项目
            'real_name' => 'required_if:pay_type,1|username|display_length:1,12',
            'grade' => [
                'required_if:pay_type,1',
                Rule::in(array_keys(User::$gradeMap))
            ],
            'receiver' => 'username|display_length:1,12',
            'province_id' => 'integer',
            'city_id' => 'integer',
            'district_id' => 'integer|exists:districts,code',
            'address_detail' => 'display_length:1,100',
            'unionid' => [
                'string',
                function ($attribute, $value, $fail) use ($user) {
                    if (! $user && ! $value) {
                        return $fail($attribute . '参数必须传！');
                    }
                }
            ],

            // 购买的课程
            'course_ids' => [
                'required',
                'array',
                new ArrayExists(Course::where('category', $categoryMap[$this->route('type')])),
                function ($attribute, $value, $fail) use ($user) {
                    if ($user) {
                        $courseIds = $user->courseUsers->pluck('course_id');
                        foreach ($value as $courseId) {
                            if ($courseIds->contains($courseId)) {
                                return $fail('课程[' . Course::find($courseId)->title . ']已经购买过了！');
                            }
                        }
                    }
                }
            ],
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
            'phone.required_if' => '请输入用户手机号码',
            'phone.cn_phone' => '请输入大陆地区合法手机号码',
            'phone.unique' => '手机号码已经存在',
            'course_ids.required' => '购买的课程必须选择',
            'course_ids.array'  => '参数非法',
            'real_name.required_if' => '请输入姓名',
            'real_name.username' => '姓名只能以非特殊字符和数字开头，不能包含特殊字符',
            'real_name.display_length' => '姓名长度不合法',
            'verifiable_code.required_if' => '请输入验证码',
            'verifiable_code.integer' => '非法的请求参数',
            'district_id.exists' => '地区参数不合法',
            'address_detail.display_length' => '详细地址长度不合法',
            'receiver.username' => '收件人只能以非特殊字符和数字开头，不能包含特殊字符',
            'receiver.display_length' => '收件人长度不合法',
            'grade.required_if' => '学员的年纪必须告诉我们哦',
            'grade.in' => '输入的年纪非法',
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
