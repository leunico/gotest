<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('name',50)->nullable()->comment('名字');
            $table->string('mobile',11)->comment('手机号');
            $table->unsignedInteger('channel_id')->default(0)->comment('渠道id');
            $table->string('ip',50)->comment('ip');
            $table->string('ip_region')->default('')->comment('ip所在地');
            $table->unsignedTinyInteger('grade')->default(0)->comment('年级');
            $table->string('user_agent')->nullable()->comment('用户代理');
            $table->string('device')->nullable()->comment('用户设备');
            $table->timestamps();
            $table->softDeletes();
            $table->index('user_id');
            $table->index('channel_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads');
    }
}
