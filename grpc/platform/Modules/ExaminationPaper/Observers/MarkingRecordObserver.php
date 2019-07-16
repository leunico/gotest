<?php

namespace Modules\ExaminationPaper\Observers;

use Modules\ExaminationPaper\Entities\MarkingRecord;
use Modules\ExaminationPaper\Jobs\MarkingStatusJob;

class MarkingRecordObserver
{
    /**
     * 处理 阅卷记录 「新建」事件。
     *
     * @param  \Modules\ExaminationPaper\Entities\MarkingRecord  $record
     * @return void
     */
    public function created(MarkingRecord $record)
    {
        // $eexaminee = $record->examinationExaminee;
        // $eexaminee->objective_score += $record->score;
        // $eexaminee->save();

        // todo 这部分队列里面处理就好了。
    }

    /**
     * 处理 阅卷记录 添加|修改」事件。
     *
     * @param  \Modules\ExaminationPaper\Entities\MarkingRecord  $record
     * @return void
     */
    public function saved(MarkingRecord $record)
    {
        $eexaminee = $record->examinationExaminee;
        $scoreName = $record->question->isChoiceQuestionCategory() ? 'objective_score' : 'subjective_score';
        if ($record && $record->getOriginal('score')){
            $eexaminee->{$scoreName} = $record->score > $record->getOriginal('score') ?
                $eexaminee->{$scoreName} + ($record->score - $record->getOriginal('score')) :
                $eexaminee->{$scoreName} - ($record->getOriginal('score') - $record->score);
        } else {
            $eexaminee->{$scoreName} = $eexaminee->{$scoreName} + $record->score;

            // 处理阅卷状态.
            MarkingStatusJob::dispatch($eexaminee);
        }

        $eexaminee->save();
    }
}
