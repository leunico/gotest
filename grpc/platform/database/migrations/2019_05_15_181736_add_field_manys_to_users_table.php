<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldManysToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('real_name', 16)->default('')->comment('真实姓名')->after('name');
            $table->timestamp('last_login_at')->nullable()->comment('最后登录时间')->after('sex');
            $table->unsignedInteger('login_count')->default(0)->comment('登录次数')->after('sex');
            $table->unsignedInteger('creator_id')->default(0)->comment('创建人')->after('sex');
            $table->boolean('account_status')->default(1)->comment('账号状态，1：可以登录，0：被禁用，不能登录')->after('sex');
            $table->string('remarks', 500)->nullable()->comment('备注')->after('sex');
            $table->unsignedTinyInteger('age')->default(0)->comment('年龄')->after('sex');
            $table->string('avatar')->nullable()->comment('头像')->after('sex');

            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
