<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestuonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('major_problems', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 64)->comment('标题');
            $table->string('description')->default('')->comment('描述');
            $table->unsignedInteger('examination_id')->comment('所属考试');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->boolean('is_question_disorder')->default(0)->comment('试题乱序');
            $table->boolean('is_option_disorder')->default(0)->comment('选项乱序');

            $table->softDeletes();
            $table->timestamps();
            $table->index('examination_id');
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('major_problem_id')->comment('所属大题');
            $table->unsignedTinyInteger('category')->comment('题目类型：1-单选题，2-判断题，3-填空题，4-操作题');
            $table->unsignedTinyInteger('score')->default(0)->comment('分值');
            $table->unsignedTinyInteger('level')->default(0)->comment('难度系数');
            $table->string('question_title', 1000)->comment('题干');
            $table->string('answer', 1000)->comment('答案');
            $table->string('knowledge', 500)->default('')->comment('知识点');
            $table->text('code')->nullable()->comment('代码');
            $table->unsignedInteger('sort')->default(0)->comment('排序');

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('question_options', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('question_id')->comment('所属问题');
            $table->string('option_title', 1000)->comment('选项内容');
            $table->boolean('is_true')->default(0)->comment('是否正确：0-否，1-是');
            $table->unsignedInteger('sort')->default(0)->comment('排序');

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
        Schema::dropIfExists('major_problems');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('question_options');
    }
}
