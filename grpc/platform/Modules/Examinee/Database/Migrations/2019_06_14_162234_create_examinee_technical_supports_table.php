<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamineeTechnicalSupportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('examinee_technical_supports', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('examination_examinee_id')->comment('考生考试');
            $table->unsignedTinyInteger('status')->default(0)->comment('状态');
            $table->string('description', 1000)->comment('描述');

            $table->timestamps();

            $table->index('examination_examinee_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('examinee_technical_supports');
    }
}
