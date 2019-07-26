<?php

namespace Modules\Educational\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Modules\Educational\Entities\StudyClass;
use Modules\Educational\Entities\ClassCourse;
use Illuminate\Support\Facades\DB;

class seedClassCourseToTable extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seedTransfer:classCourses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '填充class_courses表';

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
        $classes = StudyClass::all();
        try {
            DB::beginTransaction();
            $bar = $this->output->createProgressBar($classes->count());
            $classes->map(function ($item) use ($bar) {
                $course_ids = $item->isCategoryBigCourse() ? $item->bigCoursePivots->pluck('course_id')->toArray() : [$item->course_id];
                foreach ($course_ids as $value) {
                    ClassCourse::firstOrCreate([
                        'class_id' => $item->id,
                        'course_id' => $value
                    ]);
                }
                $bar->advance();
            });
            $bar->finish();
            DB::commit();
            $this->info('seed transfer class_course success!');
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->error($exception->getMessage());
        }
    }
}
