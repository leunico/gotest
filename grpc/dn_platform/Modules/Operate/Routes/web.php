<?php

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
use Illuminate\Contracts\Routing\Registrar as RouteContract;

Route::prefix('operate')->group(function() {
    Route::get('/', 'OperateController@index');
});

Route::group(['namespace' => 'Webs','prefix' => 'news'], function (RouteContract $api) {
    // 文章列表
    $api->get('/', 'ArticleController@index')->name('article.index');
    // 文章详情
    $api->get('show/{id}', 'ArticleController@show')->name('article.show');
});

Route::group(['namespace' => 'Webs','prefix' => 'mobile-news'], function (RouteContract $api) {
    // 文章列表
    $api->get('/', 'MobileArticleController@index')->name('mobile-article.index');
    // 文章详情
    $api->get('show/{id}', 'MobileArticleController@show')->name('mobile-article.show');
});
