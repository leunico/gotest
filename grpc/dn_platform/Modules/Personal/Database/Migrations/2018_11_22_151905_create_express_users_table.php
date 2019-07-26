<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpressUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('express_users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('用户id');
            $table->unsignedInteger('course_id')->comment('课程id');
            $table->unsignedInteger('order_id')->default(0)->comment('订单id');
            $table->unsignedTinyInteger('send_status')->default(1)->comment('1待寄件, 2部分寄件，3寄件完成 ...其他状态');
            $table->timestamps();

            $table->unique(['user_id','course_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('express_users');
    }
}
