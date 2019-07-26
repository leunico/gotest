<?php

namespace Modules\Educational\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Educational\Entities\BiuniqueAppointment;
use Modules\Educational\Entities\TeacherOfficeTime;
use App\Rules\VerifiableCode;
use Illuminate\Validation\Rule;
use App\User;
use Modules\Course\Entities\BiuniqueCourse;

class RegisterBiuniqueAppointmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     *  @author lizx
     */
    public function rules()
    {
        $user = $this->user();
        $rules = [
            'unionid' => [
                'required',
                'string',
                Rule::unique('users', 'unionid')
            ],
            'phone' => [
                'cn_phone',
                'required',
                Rule::unique('users', 'phone')
            ],
            'verifiable_code' => [
                'required_with:phone',
                'integer',
                // new VerifiableCode($this->phone)
            ],
            'real_name' => 'required_with:phone|username|display_length:1,24',
            'grade' => [
                'required_with:phone',
                Rule::in(array_keys(User::$gradeMap))
            ],
            'appointment_date' => [
                'required',
                'date',
                // function ($attribute, $value, $fail) use ($user) {
                //     if ($user && BiuniqueAppointment::where('biunique_appointments.user_id', $user->id)
                //         ->select('biunique_appointments.id')
                //         ->join('teacher_office_times', 'biunique_appointments.teacher_office_time_id', 'teacher_office_times.id')
                //         ->where('type', TeacherOfficeTime::TYPE_ST)
                //         ->where('appointment_date', $value)
                //         ->first()) {
                //         return $fail('这个时间点你已经预约过了哦~');
                //     }
                // }
            ],
            'remark' => 'max:100', // todo string| 该死的不能为空，辣鸡
            'biunique_course_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (! ($biuniqueCourse = BiuniqueCourse::select('id', 'is_audition')
                        ->where('id', $value)
                        ->where('status', BiuniqueCourse::STATUS_ON)
                        ->first())) {
                        return $fail('课程不存在啦~');
                    }

                    if ($biuniqueCourse->isNotAudition()) {
                        return $fail('该课程不提供试听~');
                    }
                }
            ]
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
            'unionid.required' => '系统参数错误',
            'phone.required' => '手机号码必须填写',
            'phone.cn_phone' => '请输入大陆地区合法手机号码',
            'phone.unique' => '手机号码已经存在',
            'real_name.required_with' => '请输入姓名',
            'real_name.username' => '姓名只能以非特殊字符和数字开头，不能包含特殊字符',
            'real_name.display_length' => '姓名长度不合法',
            'grade.required_with' => '请选择年级',
            'appointment_date.required' => '请选择时间后再预约！',
            'appointment_date.date' => '参数有误',
            'biunique_course_id.unique' => "你已经预约过这个课程了哦~",
            'unionid.unique' => "你的账号已经绑定过了哦，请先登录！~"
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
