<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProblemDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('problem_details', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('problem_id')->nullable()->comment('所属问题');
            $table->text('problem_text')->comment('问题内容');
            $table->text('answer')->nullable()->comment('答案解析');
            $table->text('hint')->nullable()->comment('作业提示');
            $table->timestamps();

            $table->index('problem_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('problem_details');
    }
}
