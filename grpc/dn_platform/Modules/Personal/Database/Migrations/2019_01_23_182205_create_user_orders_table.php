<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('target_type', 200)->comment('目标类型【模型】');
            $table->unsignedInteger('target_id')->comment('目标类型的id');
            $table->unsignedInteger('user_id')->comment('作用用户');
            $table->string('describe')->default('')->comment('描述');
            $table->tinyInteger('type')->comment('类型：1-入账，-1-支出');
            $table->unsignedInteger('amount')->comment('金额数量');
            $table->unsignedInteger('creator_id')->default(0)->comment('记录者');

            $table->timestamps();
            $table->index('user_id');
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->unique('user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('star_amount')->default(0)->comment('星星数量')->after('sex');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_orders');
    }
}
