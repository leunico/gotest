<?php

use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\Registrar as RouteContract;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// admin login
Route::post('admin-login', 'Auth\LoginController@aLogin')->name('admin-login');

// get tencent cpsv secret
Route::get('tentcent-cosv', 'Api\FileController@getTxSecret')->name('tentcent-cosv');

// 这是McIde的接口，因为是固定的链接，只能写道这里。。。辣鸡ide
// 答题[Minecraft] todo: https://exam.dev.codepku.com/api/scratch/save
Route::post('scratch/save', 'Api\ToolController@mcStore');

// Tool
Route::group([
    'namespace' => 'Api',
    'prefix' => 'tool'
], function (RouteContract $api) {
    // 获取服务器时间
    $api->get('/time', 'ToolController@time');
    // 获取跨域文件内容
    $api->get('/cross-domain', 'ToolController@crossDomain');
});

/**
 *  Auth
 */
Route::group([ 
    'namespace' => 'Auth',
    'middleware' => 'auth:api',
    'prefix' => 'auth'
], function (RouteContract $api) {
    // logout
    $api->post('logout', 'LoginController@logout')->name('logout');
    // 刷新token
    $api->post('refresh', 'LoginController@refresh')->name('refresh');
});

/**
 *  Api
 */
Route::group([
    'namespace' => 'Api',
    'middleware' => 'auth:api'
], function (RouteContract $api) {

    // files
    $api->group(['prefix' => 'file'], function (RouteContract $api) {
        // 获取文件
        $api->get('/{file}', 'FileController@show');
        // 上传文件
        $api->post('/', 'FileController@store');
    });

    // user
    $api->group(['prefix' => 'user'], function (RouteContract $api) {
        // 获取用户信息
        $api->get('/', 'UserController@userInfo');
        // 更新用户信息
        $api->put('/', 'UserController@update');
    });
});

/**
 *  Admin
 */
Route::group([
    'namespace' => 'Admin',
    'middleware' => 'auth:api',
    'prefix' => 'admin'
], function (RouteContract $api) {

    // user
    $api->group(['prefix' => 'user'], function (RouteContract $api) {
        // 获取用户列表
        $api->get('/', 'UserController@index')->middleware('gate:staff_manage_list,examination_edit');
        // 获取用户信息
        $api->get('/{user}/edit', 'UserController@edit');
        // 获取用户权限
        $api->get('/{user}/permissions', 'UserController@permissions');
        // 用户操作集
        $api->group(['middleware' => 'gate:staff_manage_edit'], function () use ($api) {
            // 创建用户
            $api->post('/', 'UserController@store')->name('admin_user_store');
            // 设置用户状态
            $api->patch('/{user}/status', 'UserController@setStatus');
            // 设置用户角色
            $api->patch('/{user}/role', 'UserController@setRole');
            // 设置用户权限
            $api->patch('/{user}/permissions', 'UserController@setPremissions');
            // 更新用户信息
            $api->put('/{user}', 'UserController@update')->name('admin_user_update');
        });
    });

    // roles
    $api->group(['prefix' => 'role'], function (RouteContract $api) {
        // 角色列表
        $api->get('/', 'RoleController@index');
        // 获取角色
        $api->get('/{role}/edit', 'RoleController@edit'); //->middleware('gate:test'); // 权限要求，可用,隔开
        // 角色设置权限
        $api->put('/{role}/permission', 'RoleController@permission'); //->middleware('role:test'); // 角色要求，可用,隔开
    });

    // permissions
    $api->group(['prefix' => 'permission'], function (RouteContract $api) {
        // 权限列表
        $api->get('/', 'PermissionController@index')->middleware('gate:staff_manage_role_permission');
        // 获取权限
        $api->get('/{permission}/edit', 'PermissionController@edit');
        // 修改权限
        $api->put('/{permission}', 'PermissionController@update')->middleware('gate:staff_manage_permission_edit');
    });
});

Route::post('/exam/video', 'ExamController@storeVideo');

Route::get('/grpc/test', 'ExamController@testGrpc');
