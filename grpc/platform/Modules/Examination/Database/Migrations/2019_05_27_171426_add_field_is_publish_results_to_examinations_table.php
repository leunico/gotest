<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldIsPublishResultsToExaminationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('examinations', function (Blueprint $table) {
            $table->boolean('is_publish_results')->default(0)->comment('是否公布成绩')->after('exam_file_id');
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
            $table->dropColumn('is_publish_results');
        });
    }
}
