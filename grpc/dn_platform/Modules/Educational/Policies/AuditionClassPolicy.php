<?php

namespace Modules\Educational\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Educational\Entities\AuditionClass;
use App\User;

class AuditionClassPolicy
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
        if ($user->isSuperAdmin()) {
            return true;
        }
    }

    /**
     * 判断否有直播课权限
     *
     * @param  \App\User  $user
     * @param  \Modules\Educational\Entities\AuditionClass $class
     * @return bool
     * @author lizx
     */
    public function show(User $user, AuditionClass $class)
    {
        return (! empty($class->status)) && ($user->id == $class->user_id || $user->id == $class->teacher_id);
    }
}
