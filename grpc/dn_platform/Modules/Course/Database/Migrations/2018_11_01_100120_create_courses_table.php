<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 100)->comment('课程标题');
            $table->string('course_intro', 255)->nullable()->comment('课程介绍');
            $table->integer('price')->nullable()->comment('课程价格');
            $table->integer('original_price')->nullable()->comment('课程原价');
            $table->tinyInteger('level')->default(1)->comment('课程所属等级');
            $table->string('course_duration', 50)->nullable()->comment('课程时长');
            $table->tinyInteger('category')->default(1)->comment('课程体系：1-艺术编程，2-数字音乐');
            $table->tinyInteger('status')->default(1)->comment('课程状态：0-未发布，1-已发布');

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
        Schema::dropIfExists('courses');
    }
}
