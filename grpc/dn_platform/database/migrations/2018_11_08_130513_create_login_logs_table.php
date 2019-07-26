<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoginLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('login_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('用户');
            $table->string('ip', 50)->comment('ip');
            $table->string('device', 100)->nullable()->comment('设备');
            $table->string('user_agent')->nullable()->comment('用户代理');
            $table->string('ip_region')->nullable()->comment('ip所在地');
            $table->string('province_id', 6)->nullable()->comment('省份');
            $table->string('city_id', 6)->nullable()->comment('城市');
            $table->string('district_id', 6)->nullable()->comment('地区');
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
        Schema::dropIfExists('login_logs');
    }
}
