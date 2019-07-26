<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProblemOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('problem_options', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('problem_id')->comment('所属问题');
            $table->text('option_text')->comment('选项内容');
            $table->tinyInteger('sort')->unsigned()->default(0)->comment('选项排序');
            $table->tinyInteger('is_true')->unsigned()->default(0)->comment('是否正确：0-否，1-是');
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
        Schema::dropIfExists('problem_options');
    }
}
