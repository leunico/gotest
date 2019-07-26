<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('user_agent', 1000)->nullable()->comment('用户代理')->change();
            $table->unsignedInteger('age')->default(0)->comment('年龄');
            $table->unsignedInteger('sex')->default(0)->comment('性别');
            $table->boolean('is_new')->default(true)->comment('是否新用户');
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
            $table->dropColumn('age');
            $table->dropColumn('sex');
            $table->dropColumn('is_new');
        });
    }
}
