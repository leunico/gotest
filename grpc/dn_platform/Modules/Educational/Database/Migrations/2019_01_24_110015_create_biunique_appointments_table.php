<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBiuniqueAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('biunique_appointments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('用户id');
            $table->unsignedInteger('teacher_office_time_id')->comment('老师的安排点');
            $table->unsignedInteger('biunique_course_id')->comment('预约的一对一课程');
            $table->unsignedInteger('creator_id')->default(0)->comment('创建人id');
            $table->unsignedSmallInteger('star_cost')->default(0)->comment('预约消耗的星星');
            $table->string('remark')->default('')->comment('备注');

            $table->timestamps();
            $table->softDeletes();
            $table->index(['teacher_office_time_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('biunique_appointments');
    }
}
