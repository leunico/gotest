<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateStatusToExaminationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('examinations', function (Blueprint $table) {
            $table->dropColumn('is_publish_results');
            $table->dropColumn('status_user_id');
            $table->dropColumn('status_time');
            $table->unsignedInteger('release_user_id')->default(0)->comment('发布人')->after('exam_file_id');
            $table->timestamp ('release_time')->nullable()->comment('发布时间')->after('exam_file_id');
            $table->unsignedTinyInteger('status')->default(0)->comment('状态：0-初始值，1-试卷发布，2-考试发布，3-成绩发布')->after('exam_file_id');
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
            // ...
        });
    }
}
