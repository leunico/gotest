<?php

namespace Modules\Educational\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Educational\Entities\Teacher;
use App\User;
use Modules\Educational\Entities\BiuniqueAppointment;
use Modules\Educational\Entities\TeacherOfficeTime;
use Illuminate\Validation\Rule;

class StoreAuditionClassPost extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     *  @author lizx
     */
    public function rules()
    {
        $query = BiuniqueAppointment::where('biunique_appointments.user_id', $this->user_id)
            ->select('biunique_appointments.id')
            ->join('teacher_office_times', 'biunique_appointments.teacher_office_time_id', 'teacher_office_times.id')
            ->when($this->route('appointment'), function ($query) {
                return $query->where('biunique_appointments.id', '!=', $this->route('appointment')->id);
            });

        return [
            'user_id' => 'required|integer', // todo exists:users,id
            'teacher_office_time_id' => [
                'required',
                'integer'
            ],
            'appointment_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($query) {
                    if ($query->where('appointment_date', $value)->first()) {
                        return $fail('用户这个时间点已经预约过了！');
                    }
                }
            ],
            'remark' => 'max:150',
            'biunique_course_id' => [
                'required',
                'integer',
                'exists:biunique_courses,id',
                function ($attribute, $value, $fail) use ($query) {
                    if ($query->where('biunique_course_id', $value)
                        ->where('type', TeacherOfficeTime::TYPE_ST)
                        ->first()) {
                        return $fail('这个课程学生已经预约过了哦~');
                    }
                }
            ]
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
            'user_id.required' => '用户需选择',
            'teacher_id.required' => '老师需选择',
            'appointment_date.required' => '预约时间必须选择',
            'remark.max' => '备注不超过150个字',
            'biunique_course_id.unique' => "学生已经预约过这个课程了哦~",
            'teacher_office_time_id.exists' => '您指定的老师没空，并不想鸟你。'
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
