<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    protected $table = 'comments';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_general_ci';

                $table->increments('id');
                $table->unsignedInteger('sender')->default(0)->comment('发送人');
                $table->unsignedInteger('receiver')->default(0)->comment('接收人');
                $table->string('type', 30)->default('')->comment('评论类型');
                $table->unsignedInteger('node_id')->default(0)->comment('节点id');
                $table->unsignedInteger('pid')->default(0)->comment('父评论id');
                $table->mediumText('content')->nullable()->comment('内容');
                $table->unsignedInteger('likes')->default(0)->comment('点赞数');
                $table->timestamps();
                $table->softDeletes();

                $table->index(['type', 'node_id']);
                $table->index('sender');
                $table->index('receiver');
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
