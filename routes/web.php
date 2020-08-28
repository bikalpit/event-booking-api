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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {
	
	//start admin login and signup routes
		$router->post('signup',  ['uses' => 'UsersController@adminSignup']);
		$router->post('login',  ['uses' => 'UsersController@isLogin']);
        //$router->post('logout',  ['uses' => 'LogoutController@isLogout']);
        $router->post('demo-test',  ['middleware'=>'auth','uses' => 'UsersController@logout']);
	//end admin login and signup routes

  		
});