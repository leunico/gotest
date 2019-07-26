<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_goods', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->string('goods_type', 100)->comment('商品类型');
            $table->unsignedInteger('goods_id')->default(0)->comment('商品id');
            $table->string('goods_title')->comment('商品标题');
            $table->unsignedInteger('goods_price')->comment('商品价格');
            $table->unsignedInteger('payment_price')->default(0)->comment('商品原价减去加权折扣');
            $table->string('goods_attr',100)->nullable()->comment('商品属性');
            $table->timestamps();
            $table->softDeletes();

            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_goods');
    }
}
