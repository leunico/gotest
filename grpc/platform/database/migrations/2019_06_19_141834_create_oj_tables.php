<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOjTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('runtimeinfo', function (Blueprint $table) {
            $table->increments('solution_id');
            $table->text('error')->nullable();
        });

        Schema::create('compileinfo', function (Blueprint $table) {
            $table->increments('solution_id');
            $table->text('error')->nullable();
        });

        Schema::create('solution', function (Blueprint $table) {
            $table->increments('solution_id');
            $table->unsignedInteger('problem_id')->default(0)->comment('题目');
            $table->unsignedInteger('user_id')->default(0)->comment('用户');
            $table->unsignedInteger('examination_examinee_id')->default(0)->comment('考生考试id');
            $table->unsignedInteger('time')->default(0);
            $table->unsignedInteger('memory')->default(0);
            $table->dateTime('in_date');
            $table->smallInteger('result')->default(0);
            $table->unsignedInteger('language')->default(0);
            $table->char('ip', 46);
            $table->unsignedInteger('contest_id')->nullable();
            $table->tinyInteger('valid')->default(1);
            $table->tinyInteger('num')->default(-1);
            $table->unsignedInteger('code_length')->default(0);
            $table->timestamp('judgetime')->nullable();
            $table->decimal('pass_rate', 3, 2)->unsigned()->default(0.00);
            $table->unsignedInteger('lint_error')->default(0);
            $table->char('judger', 16)->nullable();
        });

        Schema::create('source_code', function (Blueprint $table) {
            $table->increments('solution_id');
            $table->text('source')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('runtimeinfo');
        Schema::dropIfExists('compileinfo');
        Schema::dropIfExists('solution');
        Schema::dropIfExists('source_code');
    }
}
