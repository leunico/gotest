<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldExaminationExamineeIdToLoginLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('login_logs', function (Blueprint $table) {
            $table->unsignedInteger('examination_examinee_id')->default(0)->comment('考生记录id')->after('user_id');

            $table->index('examination_examinee_id');
        });

        Schema::table('examinees', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable()->comment('最后登录时间')->after('creator_id');
            $table->unsignedInteger('login_count')->default(0)->comment('登录次数')->after('creator_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('login_logs', function (Blueprint $table) {
            $table->dropColumn('examination_examinee_id');
        });

        Schema::table('examinees', function (Blueprint $table) {
            $table->dropColumn('last_login_at');
            $table->dropColumn('login_count');
        });
    }
}
