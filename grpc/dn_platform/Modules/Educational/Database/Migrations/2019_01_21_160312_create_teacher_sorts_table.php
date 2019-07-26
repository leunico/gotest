<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeacherSortsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_sorts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('老师');
            $table->boolean('type')->default(1)->comment('1-正式课，2-试听课');
            $table->unsignedInteger('authority_id')->comment('权限参数');
            $table->unsignedInteger('sort')->default(0)->comment('排序编号');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teacher_sorts');
    }
}
