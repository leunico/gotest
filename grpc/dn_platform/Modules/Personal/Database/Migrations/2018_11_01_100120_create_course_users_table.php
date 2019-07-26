<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseUsersTable extends Migration
{
    protected $table = 'course_users';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->default(0)->comment('用户id');
            $table->unsignedInteger('course_id')->default(0)->comment('课程id');
            $table->unsignedInteger('order_id')->default(0)->comment('订单id');
            $table->unsignedTinyInteger('status')->default(1)->comment('1有效 0无效');
            $table->string('memo')->default('')->comment('备注');
            $table->timestamps();
            $table->softDeletes();
            $table->index('user_id');
            $table->index('course_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
