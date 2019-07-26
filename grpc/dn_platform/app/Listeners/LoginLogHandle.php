<?php

namespace App\Listeners;

use Illuminate\Support\Carbon;
use App\Events\Login;
use Modules\Personal\Entities\LoginLog;
use Jenssegers\Agent\Facades\Agent;
use Zhuzhichao\IpLocationZh\Ip;
use Illuminate\Support\Facades\Request;

class LoginLogHandle
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $event->user->last_login_at = Carbon::now();
        ++$event->user->login_count;
        $event->user->save();

        $ipInfo = Ip::find(Request::getClientIp());

        $data = [
            'user_id' => $event->user->id,
            'ip' => Request::getClientIp(),
            'device' => (string) Agent::platform(),
            'user_agent' => Agent::getUserAgent(),
            'country' => $ipInfo[0],
            'province' => $ipInfo[1],
            'city' => $ipInfo[2],
            'district' => $ipInfo[3],
        ];

        // 记录
        LoginLog::create($data);
    }
}
