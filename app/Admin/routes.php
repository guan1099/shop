<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('/users',UsersController::class);
    $router->resource('/goods',GoodsController::class);
    $router->resource('/wxuser',WeixinController::class);
    $router->resource('/media',WeixinmediaController::class);
    $router->resource('/material',WeixinmaterialController::class);
    $router->get('/chat','WeixinController@chat');

    $router->post('/material','WeixinmaterialController@material');
});
