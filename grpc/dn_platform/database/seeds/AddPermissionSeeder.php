<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AddPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert(['name' => 'api-course[1]', 'guard_name' => 'api', 'category' => '观看权限', 'title' => '前台观看艺术编程课程的权限', 'description' => '前台观看艺术编程课程的权限', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'api-course[2]', 'guard_name' => 'api', 'category' => '观看权限', 'title' => '前台观看数字音乐的权限', 'description' => '前台观看数字音乐的权限', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
    }
}
