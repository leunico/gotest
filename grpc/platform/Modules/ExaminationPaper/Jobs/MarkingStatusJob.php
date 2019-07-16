<?php

namespace Modules\ExaminationPaper\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Examination\Entities\ExaminationExaminee;
use Illuminate\Support\Facades\Log;

class MarkingStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \Modules\Examination\Entities\ExaminationExaminee
     */
    protected $eexaminee;

    /**
     * @var [int]
     */
    protected $status;


    /**
     * Create a new job instance.
     *
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @return void
     */
    public function __construct(ExaminationExaminee $eexaminee)
    {
        $this->eexaminee = $eexaminee;

        $this->status = ExaminationExaminee::ACHIEVEMENT_STATUS_MARKING;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->eexaminee
            ->load([
                'markingRecord:id,examination_examinee_id,question_id',
                'examination:id',
                'examination.majorProblems:id,examination_id',
                'examination.majorProblems.questions:id,major_problem_id',
            ]);
            
        $questionIds = $this->eexaminee
            ->examination
            ->majorProblems
            ->pluck('questions')
            ->collapse()
            ->pluck('id')
            ->toArray();
        $markingIds = $this->eexaminee
            ->markingRecord
            ->pluck('question_id')
            ->toArray();
            
        if (empty(array_diff($questionIds, $markingIds)) && ! empty($this->eexaminee->achievement_status)) {
            $this->eexaminee->achievement_status = $this->status; // 你妈的为什么不能直接写 ExaminationExaminee::ACHIEVEMENT_STATUS_MARKING。。。
            $this->eexaminee->save() ? Log::notice('阅卷状态处理成功：' . $this->eexaminee->id) : Log::error('阅卷状态处理失败：' . $this->eexaminee->id);
        }
    }
}
