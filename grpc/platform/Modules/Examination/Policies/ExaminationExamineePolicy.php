<?php

namespace Modules\Examination\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Examination\Entities\ExaminationExaminee;
use Modules\Examinee\Entities\Examinee;
use Illuminate\Support\Carbon;

class ExaminationExamineePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        // ...
    }

    /**
     * 判断是否本人考试
     *
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @param \Modules\Examinee\Entities\Examinee $user
     * @return bool
     * @author lizx
     */
    public function show(Examinee $user, ExaminationExaminee $eexaminee)
    {
        if ($eexaminee->examinee_id != $user->id || ! empty($eexaminee->is_hand)) {
            return false;
        }

        return true;
    }

    /**
     * 判断是否本人考试[考试中]
     *
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @param \Modules\Examinee\Entities\Examinee $user
     * @return bool
     * @author lizx
     */
    public function middleShow(Examinee $user, ExaminationExaminee $eexaminee)
    {
        if ($eexaminee->examinee_id != $user->id || ! empty($eexaminee->is_hand)) {
            return false;
        }

        // todo 在开发完后打开
        // $eexaminee->load(['examination:id,start_at,end_at']);
        // if (Carbon::now()->lt(Carbon::parse($eexaminee->examination->start_at)) ||
        //     Carbon::now()->gt(Carbon::parse($eexaminee->examination->end_at))) {
        //     return false;
        // }

        return true;
    }

    /**
     * 判断是否本人考试的Oj
     *
     * @param \Modules\Examinee\Entities\Examinee $user
     * @return bool
     * @author lizx
     */
    public function ojShow(Examinee $user)
    {
        $eexaminee = request()->route('eexaminee');
        if (! empty($eexaminee) && ($eexaminee->examinee_id != $user->id || ! empty($eexaminee->is_hand))) {
            return false;
        }

        return true;
    }

    /**
     * 判断是否当前考试考题
     *
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @param \Modules\Examinee\Entities\Examinee $user
     * @return bool
     * @author lizx
     */
    public function questionShow(Examinee $user, ExaminationExaminee $eexaminee)
    {
        if (! $this->middleShow($user, $eexaminee)) {
            return false;
        }

        $question = request()->route('question')
            ->load([
                'majorProblem:id,examination_id'
            ]);

        if ($question->majorProblem->examination_id != $eexaminee->examination_id) {
            return false;
        }

        return true;
    }
}
