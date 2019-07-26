<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_lessons', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 100)->comment('标题，如：第一节');
            $table->unsignedInteger('work_id')->nullable()->comment('预加载作品id');
            $table->unsignedInteger('cover_id')->nullable()->comment('封面');
            $table->string('knowledge', 255)->nullable()->comment('知识点');
            $table->string('lesson_intro', 255)->nullable()->comment('课时介绍');
            $table->tinyInteger('status')->default(1)->comment('课时状态：0-未发布，1-已发布');
            $table->tinyInteger('is_public')->default(0)->comment('是否公开课：0-未公开，1-公开');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_lessons');
    }
}
