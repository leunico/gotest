<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldYouzanTradeNoToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('youzan_trade_no', 60)->nullable()->comment('有赞订单号')->after('trade_no');
            $table->boolean('finance_confirm')->default(0)->comment('财务是否确认：0-未确认，1-确认')->after('tx_num');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('youzan_trade_no');
            $table->dropColumn('finance_confirm');
        });
    }
}
