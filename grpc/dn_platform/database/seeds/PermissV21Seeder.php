<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PermissV21Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert([
            'name' => 'promote-index',
            'guard_name' => 'api',
            'category' => '软文投放管理',
            'title' => '软文投放列表',
            'description' => '软文投放列表',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('permissions')->insert([
            'name' => 'article-promote-action',
            'guard_name' => 'api',
            'category' => '软文投放管理',
            'title' => '软文投放操作',
            'description' => '软文投放的操作【包括新增加修改和有效等操作】',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('permissions')->insert([
            'name' => 'audition_class-index',
            'guard_name' => 'api',
            'category' => '一对一管理',
            'title' => '试听课列表',
            'description' => '试听课列表',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('permissions')->insert([
            'name' => 'audition_class-store',
            'guard_name' => 'api',
            'category' => '一对一管理',
            'title' => '预约试听课',
            'description' => '预约试听课',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('permissions')->insert([
            'name' => 'audition_teacher-index',
            'guard_name' => 'api',
            'category' => '一对一管理',
            'title' => '试听课老师列表',
            'description' => '试听课老师列表',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('permissions')->insert([
            'name' => 'audition_teacher-update',
            'guard_name' => 'api',
            'category' => '一对一管理',
            'title' => '试听课老师设置',
            'description' => '试听课老师设置',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
