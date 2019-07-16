<?php

namespace Modules\Examinee\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Events\MessageSent;
use Modules\Examinee\Entities\ExamineePush;

class ExamineePushMessage
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Mail\Events\MessageSent $event
     * @return void
     */
    public function handle(MessageSent $event)
    {
        if (isset($event->data['eexaminee'])) {
            $eexaminee = $event->data['eexaminee'];
            ExamineePush::create([
                'examinee_id' => $eexaminee->examinee_id,
                'pushtable_type' => 'testing_status',
                'pushtable_id' => $eexaminee->examination_id,
                'body' => $event->message->getBody()
            ]);
        }
    }
}
