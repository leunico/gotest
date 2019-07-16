<?php

namespace Modules\Examination\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Examination\Entities\Examination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExaminationExamineeRankJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \Modules\Examination\Entities\Examination
     */
    protected $examination;

    /**
     * Create a new job instance.
     *
     * @param \Modules\Examination\Entities\Examination $examination
     * @return void
     */
    public function __construct(Examination $examination)
    {
        $this->examination = $examination;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('开始处理考试排名：' . $this->examination->id);
        try {
            DB::beginTransaction();
            $this->examination->load([
                'examinationExaminees' => function ($query) {
                    $query->select('id', 'examination_id')
                        ->orderBy(DB::raw('objective_score + subjective_score'), 'desc');
                }
            ]);

            $this->examination
                ->examinationExaminees
                ->map(function ($item, $key) {
                    $item->rank = $key + 1;
                    $item->save();
                });

            DB::commit();
            Log::info('处理考试['. $this->examination->id .']排名结束');
        } catch (\Exception $exception) {
            // dd($exception);
            DB::rollBack();
            Log::error('考试排名处理异常：' . $exception->getMessage());
        }
    }
}
