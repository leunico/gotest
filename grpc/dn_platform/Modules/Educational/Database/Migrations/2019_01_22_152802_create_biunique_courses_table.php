<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBiuniqueCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('biunique_courses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 200)->comment('课程名称');
            $table->unsignedTinyInteger('category')->default(1)->comment('课程类型：1-央音，2-英皇，3-声乐，4-钢琴');
            $table->string('introduce', 500)->default('')->comment('课程介绍');
            $table->unsignedSmallInteger('price_star')->default(0)->comment('需要的星星');
            $table->boolean('status')->default(1)->comment('是否上架');
            $table->boolean('is_audition')->default(0)->comment('是否有试听课');
            $table->unsignedInteger('sort')->default(0)->comment('排序');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('biunique_courses');
    }
}
