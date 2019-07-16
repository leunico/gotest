<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExaminationExamineesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('examination_examinees', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('examinee_id')->comment('考生id');
            $table->unsignedInteger('examination_id')->comment('考试id');
            $table->unsignedInteger('verification_file')->default(0)->comment('本次考试验证文件（头像）');
            $table->string('admission_ticket', 20)->comment('准考证');
            $table->boolean('is_hand')->default(0)->comment('是否交卷');

            $table->softDeletes();
            $table->timestamps();

            $table->unique('admission_ticket');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('examination_examinees');
    }
}
