<?php

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
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {

    $api->group(['namespace' => 'App\Http\Controllers', 'middleware' => 'refresh.active_at'], function ($api) {


        $api->group(['middleware' => 'api.auth'], function ($api) {


        });

        $api->get('douyin_oauth', 'AuthController@douyinOauth')->name('douyin.oauth');
        $api->post('douyin_event', 'EventController@douyinEvent')->name('douyin.event');



    });

});
