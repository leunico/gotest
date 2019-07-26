<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldIsPushToWechatPushJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wechat_push_jobs', function (Blueprint $table) {
            $table->boolean('is_push')->default(0)->comment('是否推送过：0-否，1-是')->after('creator_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wechat_push_jobs', function (Blueprint $table) {
            $table->dropColumn('is_push');
        });
    }
}
