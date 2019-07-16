<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamineeTencentFacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('examinee_tencent_faces', function (Blueprint $table) {
            $table->increments('id');
            $table->string('request_id', 64)->comment('RequestId');
            $table->string('description')->comment('Description');
            $table->string('result', 64)->comment('Result');
            $table->float('sim', 5, 2)->comment('Sim');
            $table->unsignedInteger('examination_examinee_id')->comment('考生考试');
            $table->unsignedInteger('best_file')->default(0)->comment('最好文件');
            $table->unsignedTinyInteger('category')->comment('类型：1-活体，2-照片');
            $table->unsignedTinyInteger('type')->comment('检测时间：1-考试，2-考前');

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
        Schema::dropIfExists('examinee_tencent_faces');
    }
}
