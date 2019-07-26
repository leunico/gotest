<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SeedPermissionUnlock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('permissions')->insert([
            'name' => 'class-index',
            'guard_name' => 'api',
            'category' => '班级管理',
            'title' => '班级列表',
            'description' => '班级列表',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('permissions')->insert([
            'name' => 'class-action',
            'guard_name' => 'api',
            'category' => '班级管理',
            'title' => '班级操作',
            'description' => '班级操作',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('permissions')->insert([
            'name' => 'holiday-index',
            'guard_name' => 'api',
            'category' => '班级管理',
            'title' => '节日管理',
            'description' => '节日管理',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('permissions')->insert([
            'name' => 'holiday-action',
            'guard_name' => 'api',
            'category' => '班级管理',
            'title' => '节日操作',
            'description' => '节日操作',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('permissions')->insert([
            'name' => 'class_student-index',
            'guard_name' => 'api',
            'category' => '班级管理',
            'title' => '学员管理',
            'description' => '学员管理',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
