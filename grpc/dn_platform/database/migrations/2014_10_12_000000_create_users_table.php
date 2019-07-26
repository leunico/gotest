<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 64)->comment('名字');
            $table->string('email', 100)->nullable()->comment('电子邮箱');
            $table->string('phone', 50)->nullable()->comment('手机号码');
            $table->string('password')->comment('密码');
            $table->tinyInteger('sex')->default(0)->comment('用户性别：0-未知，1-男，2-女');
            // $table->timestamp('email_verified_at')->nullable();
            // $table->timestamp('phone_verified_at')->nullable();
            // $table->rememberToken();
            $table->timestamps();

            $table->unique('email');
            $table->unique('phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
