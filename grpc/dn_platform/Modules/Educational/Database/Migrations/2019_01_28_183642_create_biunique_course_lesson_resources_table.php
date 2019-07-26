<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBiuniqueCourseLessonResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('biunique_course_lesson_resources', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('biunique_course_lesson_id')->comment('课时id');
            $table->unsignedInteger('biunique_course_resource_id')->comment('资源');
        });

        Schema::create('biunique_appointment_files', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('biunique_appointment_id')->comment('预约id');
            $table->unsignedInteger('file_id')->comment('文件资源');
            $table->string('resource_name', 200)->default('')->comment('文件资源名称');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('biunique_course_lesson_resources');
        Schema::dropIfExists('biunique_appointment_resources');
    }
}
