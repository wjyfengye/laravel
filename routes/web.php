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

// Route::get('/', function () {
//     return view('welcome');
// });

/**
 *  拍卖，商品添加视图
 */
Route::get('/auction/create', function () {
    // echo "111";exit;
    return view('/auction/create');
});
/**
 *  登录
 */
Route::get('/auction/login', function () {
    // echo "111";exit;
    return view('/auction/login');
});

Route::post('/auction/loginDo',"Auction\LoginController@loginDo");
Route::post('/auction/add',"Auction\AuctionController@add");
Route::get('/auction/index',"Auction\AuctionController@index");
Route::get('/auction/goods/{auction_id}',"Auction\AuctionController@goods");
Route::any('/auction/addprice',"Auction\AuctionController@addPrice");
Route::get('/auction/list',"Auction\AuctionController@list");

/** 
 *   子域名  
 */
Route::domain('index.laravel.com')->namespace('Index')->group(function () {
    Route::get('index',"IndexController@index");
    Route::get('login',"IndexController@login");
    Route::get('test',"IndexController@test");
#1月10号    登录视图
    Route::get('log',"IndexController@log");
    Route::any('logDo',"IndexController@logDo");
    Route::get('wechat',"LoginController@wechat");//扫码登录
    Route::any('index',"LoginController@index");//扫码登录
    Route::any('checkWechatLogin',"LoginController@checkWechatLogin");
    
});

Route::domain('index.laravel.com')->namespace('Index')->middleware('CheckLogin')->group(function () {
    Route::get('list',"IndexController@list");
    
   
});

#1月10号    登录视图


Route::domain('api.laravel.com')->namespace('Api')->middleware('ApiMiddleware')->group(function () { 
    Route::get('api',"IndexController@index");
    Route::any('login',"IndexController@login");
});



/**
 *  第一周考  仿微信申请
 */
Route::domain('server.laravel.com')->namespace('Server')->group(function () { 
    Route::get('server',"ServerController@index");
    Route::any('apply',"ServerController@apply");//申请
    Route::any('applyDo',"ServerController@applyDo");
});

/**  仿微信   接口 */
Route::domain('api.laravel.com')->namespace('Api')->middleware('ServerMiddleware')->group(function () { 
    Route::any('index',"ServerController@index");
    
});



