<?php

namespace Modules\Examinee\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Database\Eloquent\Collection;
use Modules\Examinee\Entities\ExamineeQuestionSort;

class ExamineeQuestionSortJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 待处理数据
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $data;

    /**
     * 排序
     *
     * @var array
     */
    protected $sorts;

    /**
     * 考生考试
     *
     * @var int
     */
    protected $examination_examinee_id;

    /**
     * Create a new job instance.
     *
     * @param \Illuminate\Database\Eloquent\Collection $data
     * @param int $examination_examinee_id
     * @return void
     */
    public function __construct(int $examination_examinee_id, Collection $data)
    {
        $this->data = $data;
        $this->data
            ->map(function($item) {
                $this->sorts[$item->id] = $item->sort;
            });
        $this->examination_examinee_id = $examination_examinee_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->data
            ->map(function ($item) {
                ExamineeQuestionSort::firstOrCreate([
                    'examination_examinee_id' => $this->examination_examinee_id,
                    'sorttable_id' => $item->id,
                    'sorttable_type' => get_class($item)
                ], [
                    'sort' => $this->sorts[$item->id] ?? 0
                ]);
            });
    }
}
