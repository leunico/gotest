<?php

namespace Modules\Examinee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Examinee\Entities\Examinee;
use Modules\Examination\Entities\ExaminationExaminee;

class ExamineeResetPasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'certificate_type' => [
                'required',
                Rule::in(array_keys(Examinee::$certificateTypes))
            ],
            'admission_ticket' => [
                'required',
                'string',
                function($attribute, $value, $fail) {
                    $user = Examinee::select('examinees.id')
                        ->leftjoin('examination_examinees', 'examinees.id', 'examination_examinees.examinee_id')
                        ->where('admission_ticket', $value)
                        ->where('certificates', $this->certificates)
                        ->where('name', $this->name)
                        ->first();

                    if (empty($user)) {
                        return $fail('考生考试信息不存在，请仔细填写或者联系管理员！');
                    }

                    $this->offsetSet('examinee', $user);
                },
            ],
            'name' => [
                'required',
                'username'
            ],
            'certificates' => [
                'required',
                'string',
                'max:20'
            ],
            'password' => [
                'string',
                'max:20',
                'min:6'
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
            'admission_ticket.required' => '请输入准考证号码',
            'certificate_type.in' => '证件类型错误',
            'name.required' => '请输入用户名',
            'name.exists' => '用户名不存在',
            'name.username' => '用户名只能以非特殊字符和数字开头，不能包含特殊字符',
            'certificates.required' => '请输入证件号码',
            'certificates.exists' => '证件号码不存在',
            'password.min' => '密码不能少于6位',
            'password.max' => '密码不能大于20位',
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
