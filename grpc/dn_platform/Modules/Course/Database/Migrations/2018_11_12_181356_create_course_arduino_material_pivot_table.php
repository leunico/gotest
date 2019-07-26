<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseArduinoMaterialPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_arduino_material_pivot', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('course_id')->comment('所属系列课');
            $table->unsignedInteger('arduino_material_id')->comment('所属素材');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_arduino_material_pivot');
    }
}
