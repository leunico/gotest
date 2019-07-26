<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseSectionProblemPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_section_problem_pivot', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('problem_id')->comment('所属问题');
            $table->unsignedInteger('course_section_id')->comment('所属环节');
            $table->float('quize_time', 12, 6)->nullable()->default(0)->comment('插入环节时间');
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
        Schema::dropIfExists('course_section_problem_pivot');
    }
}
