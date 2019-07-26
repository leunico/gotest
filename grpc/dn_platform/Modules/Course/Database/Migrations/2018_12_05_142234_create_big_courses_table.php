<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBigCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('big_courses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100)->comment('课程标题');
            $table->unsignedInteger('cover_id')->default(0)->comment('封面');
            $table->tinyInteger('category')->default(1)->comment('课程体系：1-艺术编程，2-数字音乐');
            $table->tinyInteger('status')->default(0)->comment('课程状态：0-未发布，1-已发布');
            $table->string('course_intro', 255)->default('')->comment('课程介绍');

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
        Schema::dropIfExists('big_courses');
    }
}
