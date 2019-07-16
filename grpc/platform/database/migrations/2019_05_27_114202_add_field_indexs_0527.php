<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldIndexs0527 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('examinations', function (Blueprint $table) {
            $table->index(['start_at', 'end_at']);
            $table->index('examination_category_id');
            $table->index('match_id');
        });

        Schema::table('examination_examinees', function (Blueprint $table) {
            $table->index(['examinee_id', 'admission_ticket']);
            $table->index('examination_id');
        });

        Schema::table('examination_users', function (Blueprint $table) {
            $table->index('examination_id');
        });

        Schema::table('examinee_answers', function (Blueprint $table) {
            $table->index('examinee_id');
            $table->index('examination_id');
        });

        Schema::table('marking_records', function (Blueprint $table) {
            $table->index('examination_examinee_id');
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->index(['start_at', 'end_at']);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->index('major_problem_id');
        });

        Schema::table('question_options', function (Blueprint $table) {
            $table->index('question_id');
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
            $table->dropIndex(['start_at', 'end_at']);
            $table->dropIndex('examination_category_id');
            $table->dropIndex('match_id');
        });

        Schema::table('examination_examinees', function (Blueprint $table) {
            $table->dropIndex(['examinee_id', 'admission_ticket']);
            $table->dropIndex('examination_id');
        });

        Schema::table('examination_users', function (Blueprint $table) {
            $table->dropIndex('examination_id');
        });

        Schema::table('examinee_answers', function (Blueprint $table) {
            $table->dropIndex('examinee_id');
            $table->dropIndex('examination_id');
        });

        Schema::table('marking_records', function (Blueprint $table) {
            $table->dropIndex('examination_examinee_id');
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->dropIndex(['start_at', 'end_at']);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex('major_problem_id');
        });

        Schema::table('question_options', function (Blueprint $table) {
            $table->dropIndex('question_id');
        });
    }
}
