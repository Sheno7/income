<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->resource("source","SourceController");
    $router->resource("transaction","TransactionController");
    $router->resource("account","AccountController");
    $router->resource("balance","BalanceController");
    $router->resource("setting","SettingController");

});
