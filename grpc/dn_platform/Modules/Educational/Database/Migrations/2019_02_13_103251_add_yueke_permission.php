<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AddYuekePermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('permissions')->insert([
            'name' => 'biunique-teacher-list',
            'guard_name' => 'api',
            'category' => '一对一管理',
            'title' => '一对一老师列表',
            'description' => '一对一老师列表',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('permissions')->insert([
            'name' => 'biunique-teacher-action',
            'guard_name' => 'api',
            'category' => '一对一管理',
            'title' => '一对一老师操作',
            'description' => '一对一老师操作',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('permissions')->insert([
            'name' => 'biunique-teacher-appointments',
            'guard_name' => 'api',
            'category' => '一对一管理',
            'title' => '一对一老师排课总表',
            'description' => '一对一老师排课总表',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('permissions')->insert([
            'name' => 'biunique-course-list',
            'guard_name' => 'api',
            'category' => '一对一管理',
            'title' => '一对一老师课程列表',
            'description' => '一对一老师课程列表',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('permissions')->insert([
            'name' => 'biunique-course-action',
            'guard_name' => 'api',
            'category' => '一对一管理',
            'title' => '一对一老师课程操作',
            'description' => '一对一老师课程操作',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('permissions')->insert([
            'name' => 'star-package-list',
            'guard_name' => 'api',
            'category' => '一对一管理',
            'title' => '一对一老师课程价格体系列表',
            'description' => '一对一老师课程价格体系列表',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('permissions')->insert([
            'name' => 'star-package-action',
            'guard_name' => 'api',
            'category' => '一对一管理',
            'title' => '一对一老师课程价格体系操作',
            'description' => '一对一老师课程价格体系操作',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('permissions')->insert([
            'name' => 'biunique-appointments',
            'guard_name' => 'api',
            'category' => '一对一管理',
            'title' => '一对一老师课程表',
            'description' => '一对一老师课程表',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('permissions')->insert([
            'name' => 'biunique-resources-list',
            'guard_name' => 'api',
            'category' => '一对一管理',
            'title' => '一对一老师资源列表',
            'description' => '一对一老师资源列表',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('permissions')->insert([
            'name' => 'biunique-resources-action',
            'guard_name' => 'api',
            'category' => '一对一管理',
            'title' => '一对一老师资源操作',
            'description' => '一对一老师资源操作',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('permissions')->insert([
            'name' => 'biunique-student-list',
            'guard_name' => 'api',
            'category' => '一对一管理',
            'title' => '一对一老师学员列表',
            'description' => '一对一老师学员列表',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('permissions')->insert([
            'name' => 'biunique-attendance-list',
            'guard_name' => 'api',
            'category' => '一对一管理',
            'title' => '一对一老师考勤列表',
            'description' => '一对一老师考勤列表',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('permissions')->insert([
            'name' => 'biunique-attendance-action',
            'guard_name' => 'api',
            'category' => '一对一管理',
            'title' => '一对一老师考勤操作',
            'description' => '一对一老师考勤操作，不限制时间设置考勤用',
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
    }
}
