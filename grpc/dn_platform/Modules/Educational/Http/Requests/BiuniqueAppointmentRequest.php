<?php

namespace Modules\Educational\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ArrayExists;
use Illuminate\Validation\Rule;
use Modules\Educational\Entities\BiuniqueAppointment;
use Modules\Educational\Entities\TeacherOfficeTime;
use Illuminate\Support\Carbon;

class BiuniqueAppointmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     *  @author lizx
     */
    public function rules()
    {
        $appointments = BiuniqueAppointment::where('biunique_appointments.user_id', $this->user()->id)
            ->select('biunique_appointments.id', 'biunique_course_id', 'appointment_date', 'type')
            ->join('teacher_office_times', 'biunique_appointments.teacher_office_time_id', 'teacher_office_times.id')
            // ->where('type', $this->type ?? TeacherOfficeTime::TYPE_ZS)
            ->get();

        return [
            'type' => 'integer|in:' . implode(',', array_keys(TeacherOfficeTime::$typeMap)),
            'biunique_course_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($appointments) {
                    if (($appointments = $appointments->groupBy('type')->get(TeacherOfficeTime::TYPE_ST)) &&
                        $this->type == TeacherOfficeTime::TYPE_ST &&
                        $appointments->contains('biunique_course_id', $value)) {
                        return $fail('这个试听课程你已经预约过了哦~');
                    }
                }
            ],
            'appointment_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($appointments) {
                    if (Carbon::now()->gt(Carbon::parse($value)->addDay())) {
                        return $fail('你只能约现在时间一天后的课程~');
                    }

                    if ($appointments->contains('appointment_date', $value)) {
                        return $fail('这个时间点你已经预约过了哦~');
                    }
                }
            ],
            'teacher' => [
                'integer',
                Rule::exists('teacher_courses', 'user_id')
                    ->where('biunique_course_id', $this->biunique_course_id)
            ],
            'remark' => 'max:100',
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
            'appointment_date.required' => '请选择时间后再预约！',
            'appointment_date.date' => '参数有误',
            'teacher.exists' => "老师不存在或者没权限了哦",
            'biunique_course_id.unique' => "你已经预约过这个课程了哦~",
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
