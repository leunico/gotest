<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldPublishUserIdToExaminationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('examinations', function (Blueprint $table) {
            $table->unsignedInteger('status_user_id')->default(0)->comment('公布成绩人')->after('exam_file_id');
            $table->timestamp ('status_time')->nullable()->comment('公布成绩时间')->after('exam_file_id');
            $table->dropColumn('status');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedInteger('code_file')->default(0)->comment('预加载文件')->after('code');
        });

        Schema::table('examinee_answers', function (Blueprint $table) {
            $table->string('answer_file')->default('')->comment('解答文件')->after('answer');
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
            $table->dropColumn('status_user_id');
            $table->dropColumn('status_time');
            $table->boolean('status')->default(0)->comment('是否发布')->after('exam_file_id');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('code_file');
        });

        Schema::table('examinee_answers', function (Blueprint $table) {
            $table->dropColumn('answer_file');
        });
    }
}
