<?php

namespace Modules\Examinee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Examinee\Entities\Examinee;
use Modules\Examination\Entities\ExaminationExaminee;

class ExamineeLoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'password' => [
                'required',
                'string',
            ],
            'type' => [
                'required',
                'in:' . implode(',', array_keys(Examinee::$certificateTypes)),
            ],
            'admission_ticket' => [
                'required',
                'string',
                function($attribute, $value, $fail) {
                    $user = Examinee::select('examinees.id', 'examination_examinees.id as examination_examinee_id') // 'examination_examinees.status as examination_status'
                        ->leftjoin('examination_examinees', 'examinees.id', 'examination_examinees.examinee_id')
                        ->where('examination_examinees.status', ExaminationExaminee::STATUS_OK)
                        ->where('admission_ticket', $value)
                        ->where('certificate_type', $this->type)
                        ->where('certificates', $this->certificates)
                        ->first();

                    if (empty($user)) {
                        return $fail('考生考试信息不存在或未确认，请仔细填写或者联系管理员！');
                    }

                    // if (empty($user->examination_status)) {
                    //     return $fail('考生考试信息尚未确认，请联系管理员！');
                    // }

                    request()->offsetSet('examination_examinee_id', $user->examination_examinee_id);
                },
            ],
            // 'name' => [
            //     'required',
            //     'username',
            //     'exists:examinees,name'
            // ],
            'certificates' => [
                'required',
                'string',
                'max:20',
                'exists:examinees,certificates'
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
            'admission_ticket.required' => '请输入准考证号码',
            'password.required' => '密码不能为空',
            'password.min' => '密码不能少于6位',
            'password.max' => '密码不能大于20位',
            'name.required' => '请输入用户名',
            'name.exists' => '用户名不存在',
            'name.username' => '用户名只能以非特殊字符和数字开头，不能包含特殊字符',
            'certificates.required' => '请输入证件号码',
            'certificates.exists' => '证件号码不存在',
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
