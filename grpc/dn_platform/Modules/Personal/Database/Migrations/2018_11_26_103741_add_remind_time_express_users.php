<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRemindTimeExpressUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('express_users', function (Blueprint $table) {
            $table->timestamp('remind_time')->after('send_status')->nullable()->comment('提醒时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('express_users', function (Blueprint $table) {
            $table->dropColumn(['remind_time']);
        });
    }
}
