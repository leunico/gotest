<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @author lizx
     */
    public function run()
    {
        DB::table('permissions')->insert(['name' => 'course-index', 'guard_name' => 'api', 'category' => '课程管理', 'title' => '查看课程列表', 'description' => '导航栏课程列表', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'course-index:category[1]', 'guard_name' => 'api', 'category' => '课程管理', 'title' => '查看艺术编程列表', 'description' => '课程列表中艺术编程', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'course-index:category[2]', 'guard_name' => 'api', 'category' => '课程管理', 'title' => '查看数字音乐列表', 'description' => '课程列表中数字音乐', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'course-store', 'guard_name' => 'api', 'category' => '课程管理', 'title' => '添加课程', 'description' => '课程列表中添加系列课按钮', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'course-update', 'guard_name' => 'api', 'category' => '课程管理', 'title' => '编辑课程', 'description' => '课程列表中编辑按钮', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'course-action', 'guard_name' => 'api', 'category' => '课程管理', 'title' => '上/下架课程', 'description' => '课程列表中上/下架按钮', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'course-arduino', 'guard_name' => 'api', 'category' => '课程管理', 'title' => '关联元件&功能件', 'description' => '课程列表中关联元件&功能件按钮', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'course-musicTheory', 'guard_name' => 'api', 'category' => '课程管理', 'title' => '关联乐理包', 'description' => '课程列表中关联乐理包按钮', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

        DB::table('permissions')->insert(['name' => 'course_lesson-index', 'guard_name' => 'api', 'category' => '主题管理', 'title' => '管理主题', 'description' => '课程列表中查看主题按钮', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'course_lesson-store', 'guard_name' => 'api', 'category' => '主题管理', 'title' => '添加主题', 'description' => '某系列课管理主题中的添加主题按钮', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'course_lesson-update', 'guard_name' => 'api', 'category' => '主题管理', 'title' => '编辑主题', 'description' => '某系列课管理主题中的编辑主题按钮', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'course_lesson-action', 'guard_name' => 'api', 'category' => '主题管理', 'title' => '上/下架主题', 'description' => '某系列课管理主题中的上/下架按钮', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

        DB::table('permissions')->insert(['name' => 'course_section-index', 'guard_name' => 'api', 'category' => '环节管理', 'title' => '管理环节', 'description' => '课程列表中“管理环节”按钮和某系列课管理主题中的管理环节按钮', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'course_section-store', 'guard_name' => 'api', 'category' => '环节管理', 'title' => '添加环节', 'description' => '某系列课环节主题中的添加环节按钮', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'course_section-update', 'guard_name' => 'api', 'category' => '环节管理', 'title' => '编辑环节', 'description' => '某系列课环节主题中的编辑环节按钮', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'course_section-action', 'guard_name' => 'api', 'category' => '环节管理', 'title' => '上/下架环节', 'description' => '某系列课环节主题中的上/下架按钮', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

        DB::table('permissions')->insert(['name' => 'music_theory-index', 'guard_name' => 'api', 'category' => '乐理包管理', 'title' => '管理乐理包', 'description' => '导航中乐理包列表', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

        DB::table('permissions')->insert(['name' => 'arduino-index', 'guard_name' => 'api', 'category' => '元件&功能件管理', 'title' => '管理元件&功能件', 'description' => '导航中元件&功能件', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

        DB::table('permissions')->insert(['name' => 'music_practice-index', 'guard_name' => 'api', 'category' => '音乐练耳管理', 'title' => '管理音乐练耳', 'description' => '导航中音乐练耳', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

        DB::table('permissions')->insert(['name' => 'problem-index', 'guard_name' => 'api', 'category' => '题目管理', 'title' => '查看题目', 'description' => '导航中题库管理', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'problem-store', 'guard_name' => 'api', 'category' => '题目管理', 'title' => '添加题目', 'description' => '题库管理列表中添加按钮', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'problem-destroy', 'guard_name' => 'api', 'category' => '题目管理', 'title' => '删除题目', 'description' => '题库管理列表中删除按钮', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

        DB::table('permissions')->insert(['name' => 'user_manage-learnRecord', 'guard_name' => 'api', 'category' => '用户管理', 'title' => '用户观看录播课数据', 'description' => '导航中用户观看录播课数据列表', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'user_manage-statistics', 'guard_name' => 'api', 'category' => '用户管理', 'title' => '查看用户数据统计', 'description' => '导航中用户数据统计列表', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'user_manage-index', 'guard_name' => 'api', 'category' => '用户管理', 'title' => '查看用户列表', 'description' => '导航中查看用户列表列表', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'user_manage-createUser', 'guard_name' => 'api', 'category' => '用户管理', 'title' => '添加用户', 'description' => '用户列表中添加按钮', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'user_manage-setUserInfo', 'guard_name' => 'api', 'category' => '用户管理', 'title' => '编辑用户', 'description' => '用户列表中编辑按钮', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

        DB::table('permissions')->insert(['name' => 'orders-index', 'guard_name' => 'api', 'category' => '订单管理', 'title' => '查看订单列表', 'description' => '导航中订单列表', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'orders-store', 'guard_name' => 'api', 'category' => '订单管理', 'title' => '添加订单', 'description' => '订单列表中添加订单', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'orders-destroy', 'guard_name' => 'api', 'category' => '订单管理', 'title' => '删除订单', 'description' => '订单列表中删除订单', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

        DB::table('permissions')->insert(['name' => 'user_manage-status', 'guard_name' => 'api', 'category' => '用户管理', 'title' => '启用/禁用用户', 'description' => '启用/禁用用户', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'user_manage-role', 'guard_name' => 'api', 'category' => '用户管理', 'title' => '分配角色', 'description' => '分配角色', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'user_manage-premissions', 'guard_name' => 'api', 'category' => '用户管理', 'title' => '分配权限', 'description' => '分配权限', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

        DB::table('permissions')->insert(['name' => 'role-store', 'guard_name' => 'api', 'category' => '角色管理', 'title' => '添加角色', 'description' => '添加角色', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'role-update', 'guard_name' => 'api', 'category' => '角色管理', 'title' => '修改角色', 'description' => '修改角色', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'role-permission', 'guard_name' => 'api', 'category' => '角色管理', 'title' => '分配权限', 'description' => '分配权限', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'role-destroy', 'guard_name' => 'api', 'category' => '角色管理', 'title' => '删除角色', 'description' => '删除角色', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

        DB::table('permissions')->insert(['name' => 'delivery-manyMessage', 'guard_name' => 'api', 'category' => '寄件管理', 'title' => '一键提醒', 'description' => '一键提醒', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'delivery-list', 'guard_name' => 'api', 'category' => '寄件管理', 'title' => '查看寄件记录', 'description' => '查看寄件记录', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'delivery-add', 'guard_name' => 'api', 'category' => '寄件管理', 'title' => '填写寄件信息', 'description' => '填写寄件信息', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'delivery-message', 'guard_name' => 'api', 'category' => '寄件管理', 'title' => '提醒功能', 'description' => '提醒功能', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

        DB::table('permissions')->insert(['name' => 'banner-index:type[1]', 'guard_name' => 'api', 'category' => '内容管理', 'title' => '官网banner', 'description' => '官网banner', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'banner-index:type[2]', 'guard_name' => 'api', 'category' => '内容管理', 'title' => '小程序banner', 'description' => '小程序banner', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

        DB::table('permissions')->insert(['name' => 'content-menu', 'guard_name' => 'api', 'category' => '内容管理', 'title' => '内容管理左菜单', 'description' => '内容管理左菜单', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'banner-menu', 'guard_name' => 'api', 'category' => '内容管理', 'title' => 'banner管理左菜单', 'description' => 'banner管理左菜单', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'user_manage-menu', 'guard_name' => 'api', 'category' => '用户管理', 'title' => '用户管理左菜单', 'description' => '用户管理左菜单', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'user_manage-index-menu', 'guard_name' => 'api', 'category' => '用户管理', 'title' => '用户列表左菜单', 'description' => '用户列表左菜单', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'user_manage-learnRecord-menu', 'guard_name' => 'api', 'category' => '用户管理', 'title' => '观看录播课数据左菜单', 'description' => '观看录播课数据左菜单', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'user_manage-statistics-menu', 'guard_name' => 'api', 'category' => '用户管理', 'title' => '用户数据统计左菜单', 'description' => '用户数据统计左菜单', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'orders-menu', 'guard_name' => 'api', 'category' => '订单管理', 'title' => '订单管理左菜单', 'description' => '订单管理左菜单', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'orders-index-menu', 'guard_name' => 'api', 'category' => '订单管理', 'title' => '订单列表左菜单', 'description' => '订单列表左菜单', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'delivery-menu', 'guard_name' => 'api', 'category' => '订单管理', 'title' => '寄件列表左菜单', 'description' => '寄件列表左菜单', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'course-menu', 'guard_name' => 'api', 'category' => '课程管理', 'title' => '课程管理左菜单', 'description' => '课程管理左菜单', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'course-index-menu', 'guard_name' => 'api', 'category' => '课程管理', 'title' => '课程列表左菜单', 'description' => '课程列表左菜单', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'arduino-menu', 'guard_name' => 'api', 'category' => '课程管理', 'title' => '元件&功能件管理左菜单', 'description' => '元件&功能件管理左菜单', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'music_practice-menu', 'guard_name' => 'api', 'category' => '课程管理', 'title' => '视唱练耳管理左菜单', 'description' => '视唱练耳管理左菜单', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'problem-menu', 'guard_name' => 'api', 'category' => '题目管理', 'title' => '题目管理左菜单', 'description' => '题目管理左菜单', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'problem-index-menu', 'guard_name' => 'api', 'category' => '题目管理', 'title' => '题目列表左菜单', 'description' => '题目管理左菜单', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'role-menu', 'guard_name' => 'api', 'category' => '角色管理', 'title' => '角色管理左菜单', 'description' => '角色管理左菜单', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'role-index-menu', 'guard_name' => 'api', 'category' => '角色管理', 'title' => '角色列表左菜单', 'description' => '角色列表左菜单', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
    }
}
