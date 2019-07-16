<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamineeDeviceProbingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('examinee_device_probings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('examination_examinee_id')->comment('考生考试');
            $table->boolean('is_camera')->nullable()->comment('麦克风是否正常');
            $table->boolean('is_microphone')->nullable()->comment('摄像头是否正常');
            $table->boolean('is_chrome')->nullable()->comment('浏览器是否符合规定');
            $table->boolean('is_mc_ide')->nullable()->comment('MC-IDE是否正常');
            $table->boolean('is_scratch_ide')->nullable()->comment('SCRATCH-IDE是否正常');
            $table->boolean('is_python_ide')->nullable()->comment('PYTHON-IDE是否正常');
            $table->boolean('is_c_ide')->nullable()->comment('C++-IDE是否正常');

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
        Schema::dropIfExists('examinee_device_probings');
    }
}
