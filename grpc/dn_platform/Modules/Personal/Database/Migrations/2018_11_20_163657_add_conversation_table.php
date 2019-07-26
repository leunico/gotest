<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConversationTable extends Migration
{
    protected $table = 'conversation';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function (Blueprint $table) {
                $table->engine = 'Innodb';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_general_ci';

                $table->increments('id');
                $table->unsignedInteger('creator_id')->default(0)->comment('创建人id');
                $table->unsignedInteger('user_id')->default(0)->comment('用户id');
                $table->unsignedSmallInteger('type')->default(0)->comment('沟通类型，0：电话沟通，正常沟通，1：微信沟通；2：电话未接通');
                $table->mediumText('content')->nullable()->comment('沟通内容');
                $table->timestamp('conversation_at')->nullable()->comment('沟通时间');
                $table->timestamps();
                $table->softDeletes();

                $table->index(['user_id', 'type']);
                $table->index(['creator_id', 'type']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
