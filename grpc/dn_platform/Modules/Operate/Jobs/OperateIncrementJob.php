<?php

namespace Modules\Operate\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Database\Eloquent\Model;

class OperateIncrementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Model
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Field
     *
     * @var string
     */
    protected $field;

    /**
     * Create a new job instance.
     *
     * @param string $field
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function __construct(Model $model, string $field)
    {
        $this->model = $model;

        $this->field = $field;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (empty($this->field)) {
            return false;
        }

        try {
            $this->model->increment($this->field);
        } catch (\Exception $exception) {
            // dd($exception->getMessage());
            Log::error('记录模型Increment队列错误' . $exception->getMessage());
        }
    }
}
