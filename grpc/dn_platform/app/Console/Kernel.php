<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \Modules\Personal\Console\AddDeliveryMessageReminder::class,
        \Modules\Operate\Console\SyncOfficialAccountTemplates::class,
        \Modules\Operate\Console\SyncOfficialAccountUserTags::class,
        \Modules\Operate\Console\WechatPushJobHandle::class,
    ];

    /**
     *
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('AddDeliveryMessageReminder')->hourly();

        //处理模板推送【每分钟】
        $schedule->command('wechatPushJob:handle')->everyMinute();

        //同步模板、标签【每天或者自定义】
        $schedule->command('syncOfficialAccount:templates art')->daily();
        $schedule->command('syncOfficialAccount:templates music')->daily();
        $schedule->command('syncOfficialAccount:userTags art')->daily();
        $schedule->command('syncOfficialAccount:userTags music')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
