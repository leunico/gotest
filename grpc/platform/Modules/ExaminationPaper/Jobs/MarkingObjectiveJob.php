<?php

namespace Modules\ExaminationPaper\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Examination\Entities\ExaminationExaminee;
use Modules\ExaminationPaper\Entities\Marking;
use Modules\Examination\Entities\Examination;
use Modules\ExaminationPaper\Entities\MajorProblem;
use Modules\ExaminationPaper\Entities\Question;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Modules\ExaminationPaper\Entities\MarkingRecord;
use Illuminate\Support\Carbon;

class MarkingObjectiveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \Modules\Examination\Entities\ExaminationExaminee
     */
    protected $eexaminee;

    /**
     * Create a new job instance.
     *
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @return void
     */
    public function __construct(ExaminationExaminee $eexaminee)
    {
        $this->eexaminee = $eexaminee;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('开始处理考卷：' . $this->eexaminee->id);
        try {
            DB::beginTransaction();
            MajorProblem::where('examination_id', $this->eexaminee->examination_id)
                ->with([
                    'questions',
                    'questions.options' => function ($query) {
                        $query->select('id', 'question_id', 'is_true')
                            ->where('is_true', 1);
                    },
                    'questions.examineeAnswer' => function ($query) {
                        $query->select('examinee_id', 'id', 'examination_id', 'question_id', 'question_option_id', 'answer', 'answer_time', 'type')
                            ->where('examinee_id', $this->eexaminee->examinee_id);
                    },
                ])
                ->get()
                ->map(function ($item) {
                    $records = [];
                    foreach ($item->questions->toArray() as $key => $value) {
                        $records[$key] = [
                            'examination_examinee_id' => $this->eexaminee->id,
                            'question_id' => $value['id'],
                            'examination_answer_id' => $value['examinee_answer'] ? $value['examinee_answer'][0]['id'] : 0,
                            'score' => 0,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];

                        if (! empty($value['examinee_answer']) && 
                            ! empty($value['options']) && 
                            in_array($value['category'], Question::$choiceQuestionCategory)) {
                            // Log::notice('内容：', [$value['examinee_answer'], $value['options']]);
                            if (($value['category'] == Question::CATEGORY_MULTIPLE_TOPICS && 
                                $value['examinee_answer'][0]['answer'] == collect($value['options'])->sortBy('id')->implode('id', ',')) || 
                                $value['examinee_answer'][0]['question_option_id'] == $value['options'][0]['id']) {
                                $records[$key]['score'] = $value['score'];
                                $this->eexaminee->objective_score += $value['score'];
                            }
                        }
                    }

                    MarkingRecord::insert($records);
                });

            $this->eexaminee->save();
            DB::commit();
            Log::info('处理考卷['. $this->eexaminee->id .']结束，客观得分：' . $this->eexaminee->objective_score);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('考卷处理异常：' . $exception->getMessage());
        }
    }
}
