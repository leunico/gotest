<?php

use Illuminate\Contracts\Routing\Registrar as RouteContract;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// 微信网页授权
Route::get('oauth/wechat/{type}/web', 'WechatController@web')->where('type', config('wechat.official_account_no'));

Route::get('test', function () {
    $link = \Socialite::with('weixinweb')->stateless(false)->redirect();
    dd($link);
});

