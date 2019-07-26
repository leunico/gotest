<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHomeWorksTable extends Migration
{
    protected $table = 'homeworks';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function (Blueprint $table) {
                $table->engine = 'Innodb';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_general_ci';

                $table->increments('id');
                $table->unsignedInteger('course_id')->default(0)->comment('对应的课程id');
                $table->unsignedInteger('chapter_id')->default(0)->comment('对应的章节id');
                $table->unsignedInteger('lesson_id')->default(0)->comment('对应的小节id');
                $table->unsignedInteger('user_id')->default(0)->comment('对应的用户id');
                $table->unsignedSmallInteger('type')->default(0)->comment('作业类型，0:作品，1:图片，2:视频');
                $table->unsignedInteger('file_id')->default(0)->comment('图片和视频类型作业保存');
                $table->unsignedInteger('work_id')->default(0)->comment('作品类型作业保存');
                $table->unsignedInteger('views')->default(0)->comment('浏览数');
                $table->unsignedTinyInteger('is_good')->default(0)->comment('是否是优秀作业');
                $table->nullableTimestamps();
                $table->softDeletes();

                $table->index(['course_id', 'chapter_id', 'lesson_id']);
                $table->index('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
