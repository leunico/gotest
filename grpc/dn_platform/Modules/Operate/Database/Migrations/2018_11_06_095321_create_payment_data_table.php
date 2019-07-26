<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_data', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('payment_method')->default(0)->comment('1微信支付，2支付宝，3转账至公账，4POS刷卡,5现金');
            $table->string('tx_num')->comment('商户订单号');
            $table->string('tx_data',1200)->comment('交易详情');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_data');
    }
}
