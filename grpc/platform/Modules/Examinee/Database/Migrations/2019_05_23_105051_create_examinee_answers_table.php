<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamineeAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('examinee_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('examinee_id')->comment('考生');
            $table->unsignedInteger('examination_id')->comment('所属考试');
            $table->unsignedInteger('question_id')->comment('所答题目');
            $table->unsignedInteger('question_option_id')->default(0)->comment('选择选项');
            $table->text('answer')->comment('解答');
            $table->unsignedSmallInteger('answer_time')->default(0)->comment('答题相对时间');
            $table->unsignedTinyInteger('type')->default(0)->comment('题目类型，0是未知');

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
        Schema::dropIfExists('examinee_answers');
    }
}
