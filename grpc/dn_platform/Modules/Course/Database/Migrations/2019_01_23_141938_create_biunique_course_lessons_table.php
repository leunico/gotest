<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBiuniqueCourseLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('biunique_course_lessons', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('biunique_course_id')->comment('所属课程');
            $table->string('title', 200)->comment('标题');
            $table->string('introduce', 500)->default('')->comment('课时介绍');
            $table->boolean('status')->default(1)->comment('是否上下架');
            $table->unsignedInteger('sort')->default(0)->comment('排序');

            $table->timestamps();
            $table->softDeletes();

            $table->index('biunique_course_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('biunique_course_lessons');
    }
}
