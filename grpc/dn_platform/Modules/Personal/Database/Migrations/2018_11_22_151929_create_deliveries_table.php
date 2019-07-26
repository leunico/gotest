<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('express_user_id')->comment('待寄件id');
            $table->unsignedInteger('operator_id')->default(0)->comment('操作人');
            $table->unsignedTinyInteger('category')->default(0)->comment('1正常邮寄，2补寄');
            $table->string('receiver', 30)->default('')->comment('收件人');
            $table->string('province_id',10)->comment('省份id');
            $table->string('city_id', 10)->comment('城市id');
            $table->string('district_id', 10)->comment('区域id');
            $table->string('detail_address', 100)->comment('具体地址');
            $table->string('memo')->comment('备注');
            $table->timestamp('send_at')->useCurrent()->comment('寄件日期');
            $table->string('express_company')->default('')->comment('快递公司');
            $table->string('track_number', 100)->default('')->comment('快递单号');
            $table->timestamps();

            $table->index('express_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deliveries');
    }
}
