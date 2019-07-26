<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLikesTable extends Migration
{
    protected $table = 'likes';

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
                $table->unsignedInteger('user_id')->default(0)->comment('点赞人');
                $table->string('type', 30)->comment('点赞类型');
                $table->unsignedInteger('node_id')->default(0)->comment('节点id');
                $table->timestamps();
                $table->softDeletes();

                $table->index('user_id');
                $table->index(['type', 'node_id']);
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
