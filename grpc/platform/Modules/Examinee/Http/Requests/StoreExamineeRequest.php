<?php

namespace Modules\Examinee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Examinee\Entities\Examinee;
use Illuminate\Validation\Rule;
use App\Models\User;
use Modules\Examination\Entities\Examination;

class StoreExamineeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($user = $this->route('examinee')) {
            $rules = [
                'phone' => [
                    'required',
                    'cn_phone',
                    // Rule::unique('examinees', 'phone')->ignore($user->id)
                ],
                'email' => [
                    'required',
                    'email',
                    // Rule::unique('examinees', 'email')->ignore($user->id)
                ],
                'name' => [
                    'required',
                    'username',
                    'display_length:1,12'
                ],
                'certificates' => [
                    'required',
                    'string',
                    'min:6',
                    'max:20',
                    Rule::unique('examinees', 'certificates')->ignore($user->id)
                ],
                'certificate_type' => [
                    'required',
                    Rule::in(array_keys(Examinee::$certificateTypes))
                ],
                'sex' => [
                    'required',
                    Rule::in(array_keys(User::$sexMap))
                ],
                'contacts' => 'required|string',
                'birth' => 'required|string',
                'photo' => 'required|string|url|max:250',
                'certificates_photos_a' => 'required|string|url|max:250',
                'certificates_photos_b' => 'required|string|url|max:250',
                'school_name' => 'string|max:100',
                'city' => 'integer',
                'remarks' => 'required|string|max:250',
                'password' => 'max:20|min:6',

                // 考试关联
                // 'admission_ticket' => [
                //     'integer',
                //     Rule::unique('examination_examinees', 'admission_ticket')->whereNull('deleted_at')
                // ],
            ];
        } else {
            $rules = [
                'phone' => [
                    'required',
                    'cn_phone',
                    // Rule::unique('examinees', 'phone')
                ],
                'email' => [
                    'required',
                    'email',
                    // Rule::unique('examinees', 'email')
                ],
                'name' => [
                    'required',
                    'username',
                    'display_length:1,12',
                ],
                'certificates' => [
                    'required',
                    'string',
                    'min:6',
                    'max:20',
                    // Rule::unique('examinees', 'certificates')
                ],
                'certificate_type' => [
                    'required',
                    Rule::in(array_keys(Examinee::$certificateTypes))
                ],
                'sex' => [
                    'required',
                    Rule::in(array_keys(User::$sexMap))
                ],
                // 'password' => 'required|string|max:20|min:6',
                'contacts' => 'required|string',
                'birth' => 'required|string',
                'photo' => 'required|string|url|max:250',
                'certificates_photos_a' => 'required|string|url|max:250',
                'certificates_photos_b' => 'required|string|url|max:250',
                'school_name' => 'string|max:100',
                'city' => 'integer',
                'remarks' => 'required|string|max:250',

                // 考试关联
                'examination_examinee_id' => [
                    'integer',
                    Rule::exists('examination_examinees', 'id')
                        // ->where('status', Examination::STATUS_EXAMINATION)
                        ->whereNull('deleted_at')
                ],
                // 'admission_ticket' => [
                //     'integer',
                //     Rule::unique('examination_examinees', 'admission_ticket')->whereNull('deleted_at')
                // ]
            ];
        }

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
            'phone.required' => '请输入用户手机号码',
            'phone.cn_phone' => '请输入大陆地区合法手机号码',
            'phone.unique' => '手机号码已经存在',
            'password.required' => '密码不能为空',
            'password.min' => '密码不能少于6位',
            'password.max' => '密码不能大于20位',
            'email.required'  => '请输入邮箱地址',
            'email.email'  => '请输入有效的邮箱地址',
            'email.unique' => '邮箱地址已存在',
            'name.required' => '请输入用户名',
            'name.username' => '用户名只能以非特殊字符和数字开头，不能包含特殊字符',
            'name.display_length' => '用户名长度不合法',
            'name.unique' => '用户名已经被其他用户所使用',
            'remarks.required' => '考试事务咨询联络老师信息必须填写',
            'remarks.max' => '考试事务咨询联络老师信息不要超过250个字',
            'certificates.unique' => '证件号码已存在',
            'examination_id.exists' => '考试不存在',
            'examination_examinee_id.exists' => '考生考试关系不存在',
            'verification_file.exists' => '验证文件不存在',
            'admission_ticket.unique' => '准考证号码已存在'
        ];
    }
}
