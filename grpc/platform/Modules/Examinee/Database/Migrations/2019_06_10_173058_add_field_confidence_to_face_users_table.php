<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldConfidenceToFaceUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('face_users', function (Blueprint $table) {
            $table->float('confidence', 5, 2)->default(0)->comment('比对结果，数字越大表示两个人脸越可能是同一个人')->after('face_analysis_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('face_users', function (Blueprint $table) {
            $table->dropColumn('confidence');
        });
    }
}
