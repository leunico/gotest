<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassCourseLessonUnlocaksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_course_lesson_unlocaks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('class_id')->comment('班级id');
            $table->unsignedInteger('course_id')->comment('课程id');
            $table->unsignedInteger('course_lesson_id')->comment('主题id');
            $table->timestamp('unlock_day')->nullable()->comment('解锁日期');

            $table->index('class_id');
        });

        // Schema::table('course_lessons', function (Blueprint $table) {
        //     $table->dropColumn('unlock_day');
        // });

        // Schema::table('big_course_course_pivot', function (Blueprint $table) {
        //     $table->dropColumn('unlock_day');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_course_lesson_unlocaks');
    }
}
