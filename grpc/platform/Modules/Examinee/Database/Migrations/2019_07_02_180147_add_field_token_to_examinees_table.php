<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldTokenToExamineesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('examinees', function (Blueprint $table) {
            $table->string('token', 500)->default('')->after('creator_id');
        });

        // Schema::create('examinee_tokens', function (Blueprint $table) {
        //     $table->increments('id');
        //     $table->unsignedInteger('examinee_id')->comment('考生');
        //     $table->unsignedInteger('login_log_id')->comment('登陆记录');
        //     $table->string('token', 500);

        //     $table->unique('examinee_id');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('examinee_tokens');

        Schema::table('examinees', function (Blueprint $table) {
            $table->dropColumn('token');
        });
    }
}
