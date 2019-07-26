<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channels', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('category')->default(0)->comment('分类, 1(市场渠道), 2(网络运营), 3(私有渠道), 4(教学部门)');
            $table->unsignedInteger('owner_id')->default(0)->comment('归属人');
            $table->unsignedTinyInteger('level')->default(0)->comment('层级');
            $table->unsignedInteger('level1_id')->default(0);
            $table->unsignedInteger('level2_id')->default(0);
            $table->unsignedInteger('level3_id')->default(0);
            $table->unsignedInteger('parent_id')->default(0)->comment('父级id');
            $table->string('title')->comment('标题');
            $table->text('description')->comment('描述');
            $table->string('link')->nullable()->comment('原始链接');
            $table->string('slug')->unique()->comment('唯一uuid');
            $table->timestamps();
            $table->softDeletes();

            $table->index('category');
            $table->index(['level1_id','level2_id','level3_id']);

            //todo base seeder
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('channels');
    }
}
