<?php

namespace Modules\Examination\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ArrayExists;
use App\Models\User;
use Modules\Examination\Entities\Examination;

class StoreExaminationRequest extends FormRequest
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
            'title' => 'required|string|max:200',
            'start_at' => 'required|date',
            'end_at' => 'required|date',
            'age_min' => 'required|integer|min:0',
            'age_max' => 'required|integer|max:100',
            'examination_paper_title' => 'required|string|max:200',
            'examination_category_id' => 'required|exists:examination_categories,id',
            'exam_file_id' => 'required|integer|exists:files,id',
            'description' => 'max:500',
            'staffs' => [
                'required',
                'array',
                new ArrayExists(new User, true),
                function($attribute, $value, $fail) {
                    $keys = array_keys(Examination::$staffs);
                    foreach ($value as $v) {
                        if (! is_array($v) || array_diff($v, $keys)) {
                            return $fail('考组人员类型错误');
                        }
                    }
                },
            ]
        ];

        if (empty($this->route('examination'))) {
            $rules['match_id'] = 'required|exists:matches,id';
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
            'match_id.exists' => '赛事并不存在',
            'title.required' => '请输入标题',
            'start_at.required' => '请输入开始时间',
            'end_at.required' => '请输入结束时间',
            'exam_file_id.required' => '请上传考试须知',
            'exam_file_id.exists' => '考试须知文件不存在',
            'examination_category_id.required' => '类型必须选择',
            'description.max' => '备注500字以内'
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
