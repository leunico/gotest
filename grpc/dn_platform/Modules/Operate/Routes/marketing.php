<?php
/**
 * Created by PhpStorm.
 * User: ZXY
 * Date: 2019/1/17
 * Time: 12:20
 */

use Illuminate\Contracts\Routing\Registrar as RouteContract;

Route::group(['namespace' => 'Webs'], function (RouteContract $api) { // todo 现在有多个域名访问，暂时去掉这个, 'domain' => config('services.marketing_domain')
    $api->get('/central-music', 'MarketingController@centralMusic');
    $api->get('/central-music2', 'MarketingController@centralMusic2');

    $api->post('/form/save', 'MarketingController@save');

    $api->get('/music-1v1', 'MarketingController@music1V1')->middleware('wechat.oauth:music');
    $api->get('operational-affair', 'MarketingController@operationalAffair');
    $api->get('/activity', 'MarketingController@handleActivity');
    //艺术编程1对1
    $api->get('/art-code', 'MarketingController@artCode');
    $api->get('/art-code/mobile', 'MarketingController@artCodeMobile');
});

