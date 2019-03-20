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

Route::any('/test/test','Test\TestController@test');
Route::any('/test/abc','Test\TestController@abc');
Route::any('/test/add','Test\TestController@add');
Route::any('/test/list','Test\TestController@list')->middleware('check.login.token');



//注册
Route::get('/userregister','User\UserController@register');
Route::post('/userregister','User\UserController@registerdo');
//登录
Route::get('/userlogin','User\UserController@login');
Route::post('/userlogin','User\UserController@logindo');
Route::any('/userlogini','User\UserController@userLogin');
//购物车
Route::get('/cart/list','Cart\CartController@list');
Route::post('/cart/addcart','Cart\CartController@addcart')->middleware('check.cookie');
Route::get('/cart/del/{goods_id}','Cart\CartController@del');
//商品
Route::get('/goodslist','User\UserController@goodslist');
Route::get('/goodslist1','User\UserController@goodslist1');
Route::get('/goodsdetail/{id}','Goods\IndexController@index');
//订单
Route::get('/order/add','Order\OrderController@addorder')->middleware('check.login.token');
Route::get('/order/orderlist','Order\OrderController@orderlist')->middleware('check.login.token');
Route::get('/order/orderdel/{order_number}','Order\OrderController@orderdel')->middleware('check.login.token');
Route::get('/order/order/{order_number}','Order\OrderController@order')->middleware('check.login.token');
//支付
Route::get('/pay/alipay/test/{order_id}','Pay\PayController@test');        //测试
Route::get('/pay/alipay/pay','Pay\PayController@pay');   //订单支付
Route::post('/pay/alipay/paynotify','Pay\PayController@alinotify');//异步
Route::get('/pay/alipay/payreturn','Pay\PayController@alireturn');//同步
//文件上传
Route::get('/file/index','File\IndexController@index');
Route::post('/file/upload','File\IndexController@upload');
//时间测试
Route::any('test/dd',function(){
    echo date('Y-m-d H:i:s');
});

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');

//微信
Route::get('/weixin/valid1','Weixin\WeixinController@validToken1');
Route::post('/weixin/valid1','Weixin\WeixinController@wxEvent');        //接收微信服务器事件推送
Route::get('/weixin/createmenu','Weixin\WeixinController@createMenu');
Route::get('/weixin/refresh_token','Weixin\WeixinController@refreshToken'); //刷新token值
Route::get('/weixin/textgroup','Weixin\WeixinController@textGroup');
Route::get('/weixin/textmaterial','Weixin\WeixinController@textMaterial');

Route::get('/weixin/form','Weixin\WeixinController@form');
Route::post('/weixin/form','Weixin\WeixinController@material');


Route::get('/weixin/kefu','Weixin\WeixinController@keLiao');
Route::get('/weixin/kefudo','Weixin\WeixinController@keLiaodo');
Route::get('/weixin/text','Weixin\WeixinController@text');



Route::get('/weixin/pay/pay','Weixin\PayController@pay');
Route::get('/weixin/pay/order','Weixin\PayController@order');
Route::get('/weixin/pay/test/{order_id}','Weixin\PayController@test');   //支付测试
Route::post('/weixin/pay/notice','Weixin\PayController@notice');     //微信支付通知回调


Route::get('/weixin/login','Weixin\WeixinController@login');
Route::get('/weixin/getcode','Weixin\WeixinController@getCode');



Route::get('/weixin/jssdk','Weixin\WeixinController@wxJssdk');
