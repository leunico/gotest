<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBigCourseCoursePivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('big_course_course_pivot', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('big_course_id')->comment('所属大课程id');
            $table->unsignedInteger('course_id')->comment('所属课程id');
            $table->unsignedTinyInteger('sort')->default(0)->comment('系列课排序');
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
        Schema::dropIfExists('big_course_course_pivot');
    }
}
