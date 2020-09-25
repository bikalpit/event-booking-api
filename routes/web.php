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
	
	$router->post('demo-test',  ['uses' => 'UsersController@demoTest']);

	//start admin login and signup routes
	$router->post('signup',  ['uses' => 'UsersController@adminSignup']);
	$router->post('login',  ['uses' => 'UsersController@isLogin']);
  //end admin login and signup routes

  //$router->post('logout',  ['uses' => 'LogoutController@isLogout']);
	$router->post('addsaltetax-api',  ['middleware'=>'auth','uses' => 'SaleTaxController@Createsaletax']);
	$router->post('deletesaltetax-api',  ['middleware'=>'auth','uses' => 'SaleTaxController@SaleTaxDelete']);
	$router->post('updatesaltetax-api',  ['middleware'=>'auth','uses' => 'SaleTaxController@saleTaxUpdate']);
	$router->post('getsaltetax-api',  ['middleware'=>'auth','uses' => 'SaleTaxController@get_saletax_data']);
	$router->post('create-boxoffice-api',  ['middleware'=>'auth','uses' => 'BoxOfficeController@CreateBoxOffice']);
	$router->post('get-single-boxoffice-api',  ['middleware'=>'auth','uses' => 'BoxOfficeController@get_single_boxoffice_data']);
	$router->post('get-all-boxoffice-api',  ['uses' => 'BoxOfficeController@get_all_boxoffice_data']);
	$router->post('update-boxoffice-api',  ['middleware'=>'auth','uses' => 'BoxOfficeController@BoxOfficeUpdate']);
	$router->post('delete-boxoffice-api',  ['middleware'=>'auth','uses' => 'BoxOfficeController@BoxOfficeDelete']);
	$router->post('create-event-api',  ['middleware'=>'auth','uses' => 'EventController@CreateEvent']);
	$router->post('update-event-api',  ['middleware'=>'auth','uses' => 'EventController@EventUpdate']);
  $router->post('update-event-status',  ['middleware'=>'auth','uses' => 'EventController@updateEventStatus']);
	$router->post('get-single-event-api',  ['middleware'=>'auth','uses' => 'EventController@get_single_event_data']);
	$router->post('get-allboxoffice-event-api',  ['middleware'=>'auth','uses' => 'EventController@get_all_boxoffice_event_data']);
	$router->post('delete-event-api',  ['middleware'=>'auth','uses' => 'EventController@EventDelete']);
	
	
	$router->post('get-country-api',  ['uses' => 'SaleTaxController@get_all_country']);
	$router->post('get-currancy-api',  ['uses' => 'SaleTaxController@get_all_currancy']);
	
  //start customer APIs
	$router->post('create-customer-api',  ['uses' => 'CustomerController@Createcustomer']);
	$router->post('get-single-customer-api',  ['uses' => 'CustomerController@get_single_customer_data']);
	$router->post('get-all-customer-api',  ['uses' => 'CustomerController@get_all_customer_data']);
	$router->post('delete-customer-api',  ['uses' => 'CustomerController@CustomerDelete']);
	$router->post('update-customer-api',  ['uses' => 'CustomerController@CustomerUpdate']);
  $router->post('export-customers',  ['middleware'=>'auth','uses' => 'CustomerController@exportCustomers']);
  $router->post('import-customers',  ['middleware'=>'auth','uses' => 'CustomerController@importCustomers']);
  //end customer APIs

  //start forget & reset password
  $router->post('forget-password',  ['middleware'=>'auth','uses' => 'ForgetPasswordController@sendForgotEmail']);
  $router->post('reset-password',  ['middleware'=>'auth','uses' => 'ForgetPasswordController@resetPassword']);
	//end forget & reset password

  //start email verification
	$router->post('send-verification-email',  ['uses' => 'UsersController@sendVerificationEmail']);
	$router->post('send-verification-email',  ['uses' => 'UsersController@sendVerificationEmail']);
    $router->post('get-profile-api', ['uses' => 'UsersController@get_profile_data']);
    $router->post('update-profile-api', ['uses' => 'UsersController@update_profile_data']);
	//end email verification 

	$router->post('create-coupon-api',  ['uses' => 'CouponController@CreateCoupon']);
	$router->post('get-single-coupon-api',  ['uses' => 'CouponController@get_coupon_data']);
	$router->post('get-all-coupon-api',  ['uses' => 'CouponController@get_all_coupon_data']);
	$router->post('delete-coupon-api',  ['uses' => 'CouponController@CouponDelete']);
	$router->post('update-coupon-api',  ['uses' => 'CouponController@CouponUpdate']);
	$router->post('update-coupon-status-api',  ['uses' => 'CouponController@CouponStatusUpdate']);



	$router->post('create-voucher-api',  ['uses' => 'VoucherController@Createvoucher']);
	$router->post('get-single-voucher-api',  ['uses' => 'VoucherController@get_voucher_data']);
	$router->post('get-all-voucher-api',  ['uses' => 'VoucherController@get_all_voucher_data']);
	$router->post('delete-voucher-api',  ['uses' => 'VoucherController@VoucherDelete']);
	$router->post('update-voucher-api',  ['uses' => 'VoucherController@VoucherUpdate']);


	$router->post('create-broadcast-api',  ['uses' => 'BroadcastController@CreateBroadcast']);
	$router->post('get-single-broadcast-api',  ['uses' => 'BroadcastController@get_broadcast_data']);
	$router->post('get-all-broadcast-api',  ['uses' => 'BroadcastController@get_all_broadcast_data']);
	$router->post('delete-broadcast-api',  ['uses' => 'BroadcastController@BroadcastDelete']);
	$router->post('update-broadcast-api',  ['uses' => 'BroadcastController@BroadcastUpdate']);


  //start email unique validation
  $router->post('check-email',  ['uses' => 'UsersController@checkEmail']);
  //end email unique validation aman 

  //start timezone APIs
  $router->post('get-timezones',  ['uses' => 'UsersController@getTimezones']);
  //end timezone APIs

  //start ticket APIs
  $router->post('add-ticket',  ['middleware'=>'auth','uses' => 'TicketController@addTicket']);
  $router->post('update-ticket',  ['middleware'=>'auth','uses' => 'TicketController@updateTicket']);
  $router->post('get-single-ticket',  ['middleware'=>'auth','uses' => 'TicketController@getSingleTicket']);
  $router->post('delete-ticket',  ['middleware'=>'auth','uses' => 'TicketController@deleteEvent']);
  //end ticket APIs

  //get default images
  $router->post('get-default-images',  ['uses' => 'EventController@getDefaultImages']);

  //get languages
  $router->post('get-languages',  ['uses' => 'UsersController@getLanguages']);

  //strat  order confirmation api
	$router->post('set-order-confirmation-api',  ['uses' => 'EventDetailsController@createOrderConfirmation']);
	//end   order confirmation api

	//start checkout form api
	$router->post('set-checkout-form-api',  ['uses' => 'EventDetailsController@setCheckoutForm']);
	//end checkout form api 

  //get timeslots
  $router->post('get-timeslots',  ['uses' => 'EventController@getTimeslots']);

  //start order API
  $router->post('create-order',  ['uses' => 'OrderController@createOrder']);
  $router->post('get-single-order',  ['uses' => 'OrderController@getSingleOrder']);
  $router->post('cancel-order',  ['uses' => 'OrderController@cancelOrder']);
  
  //start get setting option  api
	$router->post('get-setting-option-api',  ['uses' => 'SettingsController@getOptionValue']);
	//end get setting option api 
	
	//start get setting option  api
	$router->post('get-all-setting-option-api',  ['uses' => 'SettingsController@getAllOptionsValue']);
	//end get setting option api 
});