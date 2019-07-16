<?php

namespace Modules\Examinee\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Examination\Entities\ExaminationExaminee;
use Modules\Examinee\Entities\Examinee;

class ExamineeTestingRemind extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * 考生考试.
     *
     * @var \Modules\Examination\Entities\ExaminationExaminee
     */
    public $eexaminee;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ExaminationExaminee $eexaminee)
    {
        $eexaminee->load([
            'examinee:id,certificates,certificate_type,name',
            'examination:id,title,match_id,start_at',
            'examination.match:id,title'
        ]);

        $this->eexaminee = $eexaminee;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('examinee::emails.testing')
            ->with([
                'password' => substr($this->eexaminee->examinee->certificates, -6),
                'certificate_type_str' => Examinee::$certificateTypes[$this->eexaminee->examinee->certificate_type] ?? '-',
                'url' => url('/')
            ]);
    }
}
