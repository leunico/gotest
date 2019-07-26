<?php

namespace Modules\Personal\Events;

use App\User;
use Illuminate\Queue\SerializesModels;

/**
 * Class ChangeUser
 * @package Modules\Personal\Events
 */
class ChangeUser
{
    use SerializesModels;
    /**
     * @var User
     */
    public $user;
    /**
     * 表示此事件的动作   create 表示创建  update表示更新  delete表示删除
     * @var
     */
    public $action;

    /**
     * Create a new event instance.
     *
     * @param User   $user
     * @param string $action
     */
    public function __construct(User $user, $action = 'create')
    {
        $this->user    = $user;
        $this->action = $action;
    }

    /**
     * Retrieve the paid user.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
