<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToUsersTable1108 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('channel_id')->default(0)->comment('来源渠道');
            $table->string('avatar')->nullable()->comment('头像');
            $table->unsignedTinyInteger('grade')->default(0)->comment('年级');
            $table->unsignedInteger('age')->default(0)->comment('年龄');
            $table->boolean('account_status')->default(true)->comment('账号状态，1：可以登录，0：被禁用，不能登录');
            $table->timestamp('last_login_at')->nullable()->comment('最后登录时间');
            $table->unsignedInteger('login_count')->default(0)->comment('登录次数');
            $table->unsignedInteger('creator_id')->default(0)->comment('创建人');
            $table->index('channel_id');
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
            $table->dropIndex(['channel_id']);

            $table->dropColumn('channel_id');
            $table->dropColumn('avatar');
            $table->dropColumn('grade');
            $table->dropColumn('age');
            $table->dropColumn('account_status');
            $table->dropColumn('last_login_at');
            $table->dropColumn('login_count');
            $table->dropColumn('creator_id');
        });
    }
}
