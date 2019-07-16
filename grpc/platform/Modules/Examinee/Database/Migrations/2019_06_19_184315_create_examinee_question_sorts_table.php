<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamineeQuestionSortsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('examinee_question_sorts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('examination_examinee_id')->comment('考生考试');
            $table->unsignedInteger('sorttable_id');
            $table->string('sorttable_type', 100);
            $table->unsignedSmallInteger('sort')->default(0)->comment('排序');

            $table->timestamps();
            $table->index('examination_examinee_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('examinee_question_sorts');
    }
}
