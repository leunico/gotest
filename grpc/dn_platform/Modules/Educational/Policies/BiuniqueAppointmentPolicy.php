<?php

namespace Modules\Educational\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Educational\Entities\BiuniqueAppointment;
use Illuminate\Support\Carbon;
use App\User;

class BiuniqueAppointmentPolicy
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
     * 后台设置观看权限
     *
     * @param \App\User $user
     * @return void
     */
    public function before($user)
    {
        // return true;
        if ($user->isSuperAdmin()) {
            return true;
        }
    }

    /**
     * 判断否有直播课权限
     *
     * @param  \App\User  $user
     * @param  \Modules\Educational\Entities\BiuniqueAppointment $appointment
     * @return bool
     * @author lizx
     */
    public function show(User $user, BiuniqueAppointment $appointment)
    {
        $teacherOfficeTime = $appointment->teacherOfficeTime;
        if (! $teacherOfficeTime) {
            return false;
        }

        if ($teacherOfficeTime->user_id == $user->id) {
            return true;
        }

        $now = Carbon::now();
        if ($user->id == $appointment->user_id &&
            $now->gte(Carbon::parse($teacherOfficeTime->appointment_date)->subMinutes(15)) &&
            $now->lte(Carbon::parse($teacherOfficeTime->end_date)->addMinutes(15))) {
            return true;
        }

        return false;
    }
}
