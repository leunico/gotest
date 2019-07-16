<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('creator_id')->default(0)->comment('创建人');
            $table->string('title')->comment('名称');
            $table->timestamp('start_at')->nullable()->comment('开始时间');
            $table->timestamp('end_at')->nullable()->comment('结束时间');
            $table->string('description', 500)->default('')->comment('描述');
            $table->unsignedInteger('cover_id')->default(0)->comment('封面');

            $table->softDeletes();
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
        Schema::dropIfExists('matches');
    }
}
