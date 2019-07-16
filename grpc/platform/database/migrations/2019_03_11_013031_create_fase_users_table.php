<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFaseUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('face_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('examination_examinee_id')->comment('考生考试');
            $table->unsignedInteger('file_id')->comment('图片');
            $table->string('request_id', 100)->comment('用于区分每一次请求的唯一的字符串');
            $table->unsignedInteger('face_analysis_id')->default(0)->comment('图片分析记录');
            $table->unsignedInteger('status')->default(0)->comment('状态');
            $table->timestamps();

            $table->index('examination_examinee_id');
            $table->index('request_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('face_users');
    }
}
