<?php

namespace Modules\Educational\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Modules\Personal\Entities\CourseUser;
use Modules\Educational\Entities\ClassStudent;
use Illuminate\Support\Facades\DB;

class seedClassStudentToTable extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seedTransfer:classStudent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '填充course_user_class字段.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $classStudents = ClassStudent::with(['courses'])->get();
        try {
            DB::beginTransaction();
            $bar = $this->output->createProgressBar($classStudents->count());
            $classStudents->map(function ($item) use ($bar) {
                CourseUser::where('user_id', $item->user_id)
                    ->whereIn('course_id', $item->courses->pluck('course_id')->toArray())
                    ->where('class_id', 0)
                    ->update(['class_id' => $item->class_id]);
                $bar->advance();
            });
            $bar->finish();
            DB::commit();
            $this->info('seed transfer students success!');
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->error($exception->getMessage());
        }
    }
}
