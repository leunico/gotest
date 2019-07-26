<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeacherCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_courses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('老师');
            $table->unsignedInteger('biunique_course_id')->comment('一对一课程');
            $table->unsignedTinyInteger('type')->default(1)->comment('课程类型：1-正式，2-试听');
            $table->unsignedInteger('sort')->default(0)->comment('排序编号');
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn("authority");
            $table->dropColumn("course_authority");
            $table->dropColumn("number");
        });

        Schema::table('teacher_sorts', function (Blueprint $table) {
            $table->unsignedInteger('teacher_course_id')->comment('一对一课程和老师关系')->after('user_id');
            $table->dropColumn("authority_id");
            $table->dropColumn("type");
        });

        Schema::dropIfExists('teacher_sorts');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teacher_courses');
    }
}
