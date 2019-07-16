<?php

namespace Modules\Examinee\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Examinee\Entities\ExamineeAnswer;
use Modules\Examinee\Entities\Examinee;

class ExamineeAnswerPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * 判断是否是本人解答
     *
     * @param \Modules\Examinee\Entities\ExamineeAnswer $answer
     * @param \Modules\Examinee\Entities\Examinee $user
     * @return bool
     * @author lizx
     */
    public function show(Examinee $user, ExamineeAnswer $answer)
    {
        if ($answer->examinee_id != $user->id) {
            return false;
        }

        return true;
    }
}
