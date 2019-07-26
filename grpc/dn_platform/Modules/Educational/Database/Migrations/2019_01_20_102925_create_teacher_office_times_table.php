<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeacherOfficeTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_office_times', function (Blueprint $table) {
            $table->increments('id');
            // $table->unsignedInteger('teacher_id')->default(0)->comment('老师');
            $table->unsignedInteger('user_id')->comment('老师的用户信息');
            $table->timestamp('date')->comment('日期');
            $table->char('time', 12)->comment('上课时间');
            $table->boolean('status')->default(0)->comment('状态：0-可约，1-已约');
            $table->unsignedInteger('sort')->default(0)->comment('排序');

            // $table->unique(['user_id', 'date']);
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
        Schema::dropIfExists('teacher_office_times');
    }
}
