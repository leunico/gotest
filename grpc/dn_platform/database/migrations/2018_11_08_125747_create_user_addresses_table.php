<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('用户id');
            $table->string('province_id', 6)->nullable()->comment('省份');
            $table->string('city_id', 6)->nullable()->comment('城市');
            $table->string('district_id', 6)->nullable()->comment('地区');
            $table->string('detail', 100)->nullable()->comment('具体');
            $table->timestamps();

            $table->unique('user_id');
            $table->index('province_id');
            $table->index('city_id');
            $table->index('district_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_addresses');
    }
}
