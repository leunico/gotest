<?php

/**
 * 运营相关后台接口
 */

use Illuminate\Contracts\Routing\Registrar as RouteContract;

Route::group([
    'namespace' => 'Admins'
], function (RouteContract $api) {
    Route::group(['middleware' => ['auth:api'],], function () use ($api) {

        // 订单管理
        $api->group(['prefix' => 'orders'], function () use ($api) {
            //订单列表
            $api->get('/', 'OrderController@index')->middleware('role:orders-index');
            //导出订单列表
            $api->get('/export', 'ExportController@orders')->middleware('role:orders-index');
            //订单详情
            $api->get('/{order}/edit', 'OrderController@edit');
            //创建订单
            $api->post('/', 'OrderController@store')->middleware('role:orders-store');
            //删除订单
            $api->delete('/{order}', 'OrderController@destroy')->middleware('role:orders-destroy');
            //财务确认订单
            $api->patch('/{order}/confirm', 'OrderController@financeConfirm');
            //修改订单
            $api->put('/{order}', 'OrderController@update');

            $api->post('star-packages', 'OrderController@storeStarPackage');
        });

        // Banner管理
        $api->group(['prefix' => 'banner'], function () use ($api) {
            //banner列表
            $api->get('/', 'BannerController@index')->middleware('role:banner-index:type');
            //banner详情
            $api->get('/show/{banner}', 'BannerController@show');
            //创建banner
            $api->post('/', 'BannerController@store');
            //删除banner
            $api->delete('/{banner}', 'BannerController@destroy');
            //修改banner
            $api->put('/update/{banner}', 'BannerController@update');
        });

        // 公众号管理
        $api->group(['prefix' => 'official-account'], function () use ($api) {
            //模板列表
            $api->get('/{type}/templates', 'OfficialAccountController@templates')->where('type', config('wechat.official_account_no'));
            //同步微信内容
            $api->post('/{type}/sync', 'OfficialAccountController@sync')->middleware('throttle:20')->where('type', config('wechat.official_account_no'));
            //用户标签列表
            $api->get('/{type}/user-tags', 'OfficialAccountController@userTags')->where('type', config('wechat.official_account_no'));
            //推送消息管理
            $api->group(['prefix' => 'push'], function () use ($api) {
                //添加推送消息
                $api->post('/', 'OfficialAccountController@pushStore')->middleware('role:official_account-store:category');
                //消息推送列表
                $api->get('/{type}', 'OfficialAccountController@pushs')->middleware('role:official_account-index|type')->where('type', config('wechat.official_account_no'));
                //推送详情
                $api->get('/{push}/edit', 'OfficialAccountController@pushEdit');
                //修改推送
                $api->put('/{push}', 'OfficialAccountController@pushUpdate');
                //删除推送
                $api->delete('/{push}', 'OfficialAccountController@pushDestroy');
            });
        });

        // 运营推广管理
        $api->group(['prefix' => 'promote'], function () use ($api) {
            //软文推广管理
            $api->group(['prefix' => 'article-promote'], function () use ($api) {
                //添加软文推广
                $api->post('/', 'ArticlePromoteController@store');
                //软文推广列表
                $api->get('/', 'ArticlePromoteController@index');
                //软文推广详情
                $api->get('/{promote}/edit', 'ArticlePromoteController@edit');
                //修改软文推广
                $api->put('/{promote}', 'ArticlePromoteController@update');
                //启用软文推广
                $api->patch('/{promote}/action', 'ArticlePromoteController@action');
                //删除软文推广
                $api->delete('/{promote}', 'ArticlePromoteController@destroy');
            });
        });

        // 文章管理
        $api->group(['prefix' => 'article'], function () use ($api) {
            // 创建文章
            $api->post('/', 'ArticleController@store')->middleware('role:add-news-article');
            // 文章列表
            $api->get('/', 'ArticleController@index');
            // 文章详情
            $api->get('show/{article}', 'ArticleController@show')->middleware('role:see-news-article');
            // 修改文章
            $api->put('update/{article}', 'ArticleController@update')->middleware('role:add-news-article');
            // 删除文章
            $api->delete('/{article}', 'ArticleController@destroy')->middleware('role:delete-news-article');
            // 发布或者取消发布
            $api->put('/{article}', 'ArticleController@status')->middleware('role:release-news-article');
        });


        $api->group(['prefix' => 'leads',], function () use ($api) {
            //查看线索
            $api->get('/', 'LeadsController@index')->middleware('role:leads-view');
            //查看线索
            $api->get('/dn-one2one-list', 'LeadsController@dnOne2OneList')->middleware('role:leads-view');

        });
    });
});
