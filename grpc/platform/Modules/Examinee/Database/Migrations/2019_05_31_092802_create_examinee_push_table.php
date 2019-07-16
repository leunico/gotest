<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamineePushTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('examinee_pushes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('examinee_id')->comment('考生');
            $table->string('pushtable_type', 150)->comment('推送类型');
            $table->unsignedInteger('pushtable_id')->comment('推送数据id');
            $table->text('body')->nullable()->comment('推送内容');

            $table->timestamps();
            $table->index('examinee_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('examinee_pushes');
    }
}
