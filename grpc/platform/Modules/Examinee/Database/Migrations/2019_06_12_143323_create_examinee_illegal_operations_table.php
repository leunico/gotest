<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamineeIllegalOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('examinee_operations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('examination_examinee_id')->comment('考试');
            $table->unsignedTinyInteger('category')->default(0)->comment('操作类型:1-切屏');
            $table->string('remark')->default('')->comment('操作备注');

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
        Schema::dropIfExists('examinee_operations');
    }
}
