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
| START Routes for Admin actions
|--------------------------------------------------------------------------
*/
$router->group(['namespace' => 'Admin', 'prefix' => 'admin/api'], function($router) {
    /*Users*/
    $router->controller('users', 'AdminUserController');

    /*Pages*/
    $router->controller('pages', 'AdminPageController');

    /*Categories*/
    $router->controller('categories', 'AdminCategoryController');

    /*Settings*/
    $router->controller('settings', 'AdminSettingController');

    /*Other*/
    $router->controller('languages', 'AdminLanguageController');
    $router->controller('files', 'AdminFileController');
});
/*
|--------------------------------------------------------------------------
| END Routes for Admin actions
|--------------------------------------------------------------------------
*/