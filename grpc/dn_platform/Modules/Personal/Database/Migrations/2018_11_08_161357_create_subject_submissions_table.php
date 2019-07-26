<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubjectSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subject_submissions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('用户ID');
            $table->unsignedSmallInteger('section_id')->comment('环节id');
            $table->unsignedSmallInteger('problem_id')->comment('题目ID');
            $table->string('answer_id',20)->comment('答案ID');
            $table->unsignedTinyInteger('type')->default(1)->comment('1能判断正确的题目，2操作题类的');
            $table->unsignedTinyInteger('is_correct')->default(0)->comment('是否是正确答案，0是错误，1是正确');
            $table->timestamps();
            $table->index(['user_id', 'section_id'], 'user_section');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subject_submissions');
    }
}
