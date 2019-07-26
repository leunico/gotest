<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseKnobblesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_sections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100)->comment('小节名称');
            $table->unsignedInteger('course_lesson_id')->nullable()->comment('课程课时id');
            $table->tinyInteger('category')->nullable()->comment('小节类别：1-视频(有编辑器)，2-纯视频教程，3-练一练');
            $table->tinyInteger('status')->default(1)->comment('小节状态：0-未发布，1-已发布');
            $table->integer('section_number')->nullable()->comment('小节编号');
            $table->text('section_intro')->nullable()->comment('小节介绍');

            $table->softDeletes();
            $table->timestamps();

            $table->index('course_lesson_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_sections');
    }
}
