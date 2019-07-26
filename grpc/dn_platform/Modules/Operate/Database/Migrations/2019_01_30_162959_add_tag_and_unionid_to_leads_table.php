<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTagAndUnionidToLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('tag', 100)->nullable()->comment('标签');
            $table->string('unionid', 100)->nullable()->comment('微信id');
            $table->text('ext_data')->comment('额外数据');
            $table->unsignedInteger('operational_affair')->default(0)->comment('运营教务');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('tag');
            $table->dropColumn('unionid');
            $table->dropColumn('ext_data');
            $table->dropColumn('operational_affair');
        });
    }
}
