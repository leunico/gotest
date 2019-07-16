<?php

namespace Modules\Examinee\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Auth\Events\Login;
use App\Listeners\LoginLogHandle;
use Illuminate\Mail\Events\MessageSent;
use Modules\Examinee\Listeners\ExamineePushMessage;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        MessageSent::class => [
            ExamineePushMessage::class,
        ],
    ];
}
