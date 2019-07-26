<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('用户id');
            $table->string('trade_no', 60)->comment('订单号');
            $table->unsignedTinyInteger('payment_method')->default(0)->comment('1微信支付，2支付宝，3转账至公账，4POS刷卡,5现金');
            $table->unsignedInteger('discount')->default(0)->comment('折扣');
            $table->unsignedInteger('total_price')->default(0)->comment('总价');
            $table->unsignedInteger('real_price')->default(0)->comment('应付金额');
            $table->boolean('is_paid')->default(false)->comment('是否支付');
            $table->timestamp('paid_at')->nullable()->comment('支付时间');
            $table->string('memo')->nullable()->comment('备注');
            $table->timestamp('expired_at')->nullable()->comment('过期时间');

            $table->string('ext_data',1000)->nullable()->comment('额外数据');

            //用户微信支付 todo 需了解如何使用
            $table->string('prepay_id', 64)->nullable()->comment('预支付交易会话标识');
            $table->string('trade_type',16)->nullable()->comment('JSAPI,NATIVE等');
            $table->string('tx_num',100)->nullable()->comment('商户订单号');
            $table->timestamps();
            $table->softDeletes();

            $table->unique('trade_no');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
