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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/adduser','User\UserController@add');

//路由跳转
Route::redirect('/hello1','/world1',301);
Route::get('/world1','Test\TestController@world1');

Route::get('hello2','Test\TestController@hello2');
Route::get('world2','Test\TestController@world2');


//路由参数
Route::get('/user/{uid}','User\UserController@user');
Route::get('/month/{m}/date/{d}','Test\TestController@md');
Route::get('/name/{str?}','Test\TestController@showName');



// View视图路由
Route::view('/mvc','mvc');
Route::view('/error','error',['code'=>403]);


// Query Builder
Route::get('/query/get','Test\TestController@query1');
Route::get('/query/where','Test\TestController@query2');


Route::any('/test/abc','Test\TestController@abc');
Route::any('/test/add','Test\TestController@add');
Route::any('/test/list','Test\TestController@list')->middleware('check.login.token');



//注册
Route::get('/userregister','User\UserController@register');
Route::post('/userregister','User\UserController@registerdo');
//登录
Route::get('/userlogin','User\UserController@login');
Route::post('/userlogin','User\UserController@logindo');
//购物车
Route::get('/cart/list','Cart\CartController@list')->middleware('check.login.token');
Route::post('/cart/addcart','Cart\CartController@addcart')->middleware('check.cookie');
Route::get('/cart/del/{goods_id}','Cart\CartController@del')->middleware('check.login.token');
//商品
Route::get('/goodslist','User\UserController@goodslist');
Route::get('/goodsdetail/{id}','Goods\IndexController@index');
//订单
Route::any('/order/add','Order\OrderController@addorder')->middleware('check.login.token');
Route::any('/order/orderlist','Order\OrderController@orderlist')->middleware('check.login.token');
Route::any('/order/orderdel/{order_number}','Order\OrderController@orderdel')->middleware('check.login.token');
Route::any('/order/order/{order_number}','Order\OrderController@order')->middleware('check.login.token');
//支付
Route::get('/pay/alipay/test','Pay\payController@test')->middleware('check.login.token');        //测试
Route::get('/pay/alipay/pay','Pay\PayController@pay');   //订单支付
Route::post('/pay/alipay/notify','Pay\PayController@notify');
//时间测试
Route::any('test/dd',function(){
    echo date('Y-m-d H:i:s');
});
