<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_courses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('course_id')->comment('课程');
            $table->unsignedInteger('class_id')->comment('班级');
        });

        Schema::table('course_users', function (Blueprint $table) {
            $table->unsignedInteger('class_id')->default(0)->comment('班级')->after('memo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_courses');
        Schema::table('course_users', function (Blueprint $table) {
            $table->dropColumn('class_id');
        });
    }
}
