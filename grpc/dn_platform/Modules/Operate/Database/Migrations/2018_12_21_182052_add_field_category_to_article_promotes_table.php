<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldCategoryToArticlePromotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('article_promotes', function (Blueprint $table) {
            $table->tinyInteger('category')->default(1)->comment('课程体系：1-艺术编程，2-数字音乐')->after('title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('article_promotes', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
}
