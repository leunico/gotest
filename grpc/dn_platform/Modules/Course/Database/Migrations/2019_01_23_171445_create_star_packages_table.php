<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStarPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('star_packages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 200)->comment('名称');
            $table->unsignedSmallInteger('count_lesson')->default(0)->comment('课时包数量');
            $table->unsignedInteger('price')->default(0)->comment('价格');
            $table->unsignedSmallInteger('star')->default(0)->comment('星星数量');
            $table->boolean('status')->default(1)->comment('上下架状态');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('star_packages');
    }
}
