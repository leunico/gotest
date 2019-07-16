<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarkingRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marking_records', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('examination_examinee_id')->comment('考生考试id');
            $table->unsignedInteger('question_id')->comment('题目id');
            $table->unsignedInteger('examination_answer_id')->default(0)->comment('答题id');
            $table->unsignedInteger('user_id')->default(0)->comment('阅卷人，0是系统阅卷');
            $table->unsignedSmallInteger('score')->default(0)->comment('阅卷打分');

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
        Schema::dropIfExists('marking_records');
    }
}
