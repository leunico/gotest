<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldQualificationUserToExaminationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('examinations', function (Blueprint $table) {
            $table->unsignedInteger('qualification_user_id')->default(0)->comment('确认考试资格人')->after('release_user_id');
            $table->timestamp('qualification_time')->nullable()->comment('确认考试资格时间')->after('release_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('examinations', function (Blueprint $table) {
            $table->dropColumn('qualification_user_id');
            $table->dropColumn('qualification_time');
        });
    }
}
