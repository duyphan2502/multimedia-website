<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/admin', function () {
    return view('admin.master');
});

/*
|--------------------------------------------------------------------------
| START Routes for API actions
|--------------------------------------------------------------------------
*/
$router->group(['namespace' => 'Admin', 'prefix' => 'admin/api', 'middleware' => 'cors'], function($router) {
    /*User*/
    $router->controller('users', 'AdminUserController');

    /*Page*/
    $router->controller('pages', 'AdminPageController');

    /*Other*/
    $router->controller('languages', 'AdminLanguageController');
    $router->controller('files', 'AdminFileController');
});
/*
|--------------------------------------------------------------------------
| END Routes for API actions
|--------------------------------------------------------------------------
*/