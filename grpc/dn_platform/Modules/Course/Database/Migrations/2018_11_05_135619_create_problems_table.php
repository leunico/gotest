<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProblemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('problems', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->tinyInteger('category')->nullable()->unsigned()->comment('题目类型：1-单选题，2-判断题，3-多选题，4-操作题，5-问答题');
            $table->tinyInteger('course_category')->nullable()->unsigned()->comment('课程体系：1-艺术编程，2-数字音乐');
            $table->unsignedInteger('preview_id')->nullable()->comment('预览文件');
            $table->unsignedTinyInteger('use_count')->default(0)->comment('使用次数');
            $table->unsignedSmallInteger('plan_duration')->default(0)->comment('计划用时');
            $table->softDeletes();
            $table->timestamps();

            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('problems');
    }
}
