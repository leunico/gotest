<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamainationUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('examination_users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('examination_id')->comment('考试id');
            $table->unsignedInteger('user_id')->comment('用户');
            $table->unsignedTinyInteger('type')->comment('员工类型');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('examination_users');
    }
}
