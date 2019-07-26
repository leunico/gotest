<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldVideoLinkToMusicTheoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('music_theories', function (Blueprint $table) {
            $table->dropColumn('file_id');
            $table->string('source_link')->nullable()->comment('资源链接')->after('name');
            $table->unsignedInteger('source_duration')->default(0)->comment('资源时长（视频、音频）')->after('source_link');
            $table->integer('sort')->default(0)->comment('排序')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('music_theories', function (Blueprint $table) {
            $table->unsignedInteger('file_id')->nullable()->comment('文件')->after('name');
            $table->dropColumn('source_link');
            $table->dropColumn('source_duration');
            $table->dropColumn('sort');
        });
    }
}
