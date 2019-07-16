<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExaminationSimulationPapersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('examination_simulation_papers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('examination_category_id')->default(0)->comment('类型');
            $table->text('content')->nullable()->comment('题目');
            $table->unsignedInteger('creator_id')->default(0)->comment('创建人');

            $table->timestamps();
            $table->unique('examination_category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('examination_simulation_papers');
    }
}
