<?php

namespace Modules\Examination\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Examinee\Entities\Examinee;
use Modules\Examination\Entities\Examination;
use Illuminate\Support\Carbon;
use Modules\Examinee\Entities\ExamineeTencentFace;
use Illuminate\Http\Request;
use Modules\Examination\Entities\ExaminationExaminee;
use Illuminate\Support\Facades\DB;

class ExaminationPolicy
{
    use HandlesAuthorization;

    /**
     * Request
     *
     * @var Illuminate\Http\Request
     */
    public $request;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->request = request();
    }


    /**
     * 判断是否有本场考试权限
     *
     * @param \Modules\Examinee\Entities\Examinee $user
     * @return bool
     * @author lizx
     */
    public function show(Examinee $user)
    {
        $admissionTicket = $this->request->route('admissionTicket');
        $examination = Examination::select(
            'examinations.id',
            'match_id',
            'examination_category_id',
            'title',
            'examination_paper_title',
            'examination_examinees.id as examination_examinee_id',
            'start_at',
            'end_at',
            'examination_examinees.status',
            'description',
            'exam_file_id',
            'is_hand',
            'testing_status'
        )
            ->leftjoin('examination_examinees', 'examinations.id', 'examination_examinees.examination_id')
            ->where('examinee_id', $user->id)
            ->where('admission_ticket', $admissionTicket)
            ->where('examinations.status', Examination::STATUS_EXAMINATION)
            ->first();

        if (empty($examination)) {
            $this->deny('考生考试不存在或考试未发布！');
        }

        // todo 这里要加很多判断
        // if (Carbon::now()->lt(Carbon::parse($examination->start_at)) || Carbon::now()->gt(Carbon::parse($examination->end_at))) {
        //     $this->deny('不在考试时间内！');
        // }

        // 交卷不可再请求
        if (! empty($examination->is_hand)) {
            $this->deny('你已经交卷了亲。');
        }

        // 考前测试未通过（待商榷）
        // if (empty($examination->testing_status)) {
        //     return false;
        // }

        // 未通过人脸识别
        // if (! ExamineeTencentFace::where('examination_examinee_id', $examination->examination_examinee_id)
        //     ->where('type', ExamineeTencentFace::CATEGORY_RECOGNITION)
        //     ->where('result', 'Success')
        //     ->first()) {
        //     $this->deny('你未通过人脸核身。');
        // }

        $this->request->offsetSet('examination', $examination);
        return true;
    }

    /**
     * 我的考试页面
     *
     * @param \Modules\Examinee\Entities\Examinee $user
     * @return bool
     * @author lizx
     */
    public function detail(Examinee $user)
    {
        $admissionTicket = $this->request->route('admissionTicket');
        $eexaminee = ExaminationExaminee::select(
            'examinations.id as examination_id',
            'title',
            'examination_category_id',
            'examination_paper_title',
            'examination_examinees.id',
            'start_time',
            'start_at',
            'end_at',
            'exam_file_id',
            'is_hand',
            'examinations.status',
            'admission_ticket',
            'rank',
            'achievement_status',
            'objective_score',
            'subjective_score',
            'testing_status',
            'origin_filename',
            'driver_baseurl',
            'filename',
            DB::raw(
                "(select sum(total_score) 
                from major_problems 
                where major_problems.examination_id = examination_examinees.examination_id and deleted_at is null
                ) as total_score"
            ))
            ->leftjoin('examinations', 'examination_examinees.examination_id', 'examinations.id')
            ->leftjoin('files', 'examinations.exam_file_id', 'files.id')
            ->where('examinee_id', $user->id)
            ->where('admission_ticket', $admissionTicket)
            ->whereIn('examinations.status', [Examination::STATUS_EXAMINATION, Examination::STATUS_ACHIEVEMENT])
            ->first();

        if (empty($eexaminee)) {
            return false;
        }
        
        $this->request->offsetSet('eexaminee', $eexaminee);
        return true;
    }
}
