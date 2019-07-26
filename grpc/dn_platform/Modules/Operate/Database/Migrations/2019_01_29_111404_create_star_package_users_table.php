<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStarPackageUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('star_package_users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('用户');
            $table->unsignedInteger('star_package_id')->comment('星星包');
            $table->string('memo')->default('')->comment('备注');
            $table->unsignedInteger('creator_id')->default(0)->comment('创建人');
            $table->unsignedInteger('order_id')->default(0)->comment('订单id');
            $table->boolean('status')->default(1)->comment('是否生效状态');

            $table->timestamps();
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
        Schema::dropIfExists('star_package_users');
    }
}
