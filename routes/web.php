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
	$router->post('logout',  ['uses' => 'LogoutController@isLogout']);
	$router->post('check-token',  ['uses' => 'UsersController@checkToken']);
	$router->post('user-re-login',  ['uses' => 'UsersController@userReLogin']);
  //end admin login and signup routes

	//start tax api
	$router->post('addsaltetax-api',  ['middleware'=>'auth','uses' => 'SaleTaxController@Createsaletax']);
	$router->post('deletesaltetax-api',  ['middleware'=>'auth','uses' => 'SaleTaxController@SaleTaxDelete']);
	$router->post('updatesaltetax-api',  ['middleware'=>'auth','uses' => 'SaleTaxController@saleTaxUpdate']);
  $router->post('getsaltetax-api',  ['middleware'=>'auth','uses' => 'SaleTaxController@get_saletax_data']);
	$router->post('get-single-tax',  ['middleware'=>'auth','uses' => 'SaleTaxController@getSingleTax']);
	//end tax api
	
	//start boxoffice api
	$router->post('create-boxoffice-api',  ['middleware'=>'auth','uses' => 'BoxOfficeController@CreateBoxOffice']);
	$router->post('get-single-boxoffice-api',  ['middleware'=>'auth','uses' => 'BoxOfficeController@get_single_boxoffice_data']);
	$router->post('get-all-boxoffice-api',  ['middleware'=>'auth','uses' => 'BoxOfficeController@get_all_boxoffice_data']);
	$router->post('update-boxoffice-api',  ['uses' => 'BoxOfficeController@BoxOfficeUpdate']);
	$router->post('delete-boxoffice-api',  ['middleware'=>'auth','uses' => 'BoxOfficeController@BoxOfficeDelete']);
	$router->post('remove-boxoffice-api',  ['uses' => 'BoxOfficeController@removeImage']);
	//end boxoffice api
	
	//start Events api
	$router->post('create-event-api',  ['middleware'=>'auth','uses' => 'EventController@CreateEvent']);
	$router->post('update-event-api',  ['uses' => 'EventController@EventUpdate']);
  $router->post('update-event-status',  ['middleware'=>'auth','uses' => 'EventController@updateEventStatus']);
	$router->post('get-single-event-api',  ['uses' => 'EventController@get_single_event_data']);
	$router->post('get-allboxoffice-event-api',  ['middleware'=>'auth','uses' => 'EventController@get_all_boxoffice_event_data']);
	$router->post('delete-event-api',  ['middleware'=>'auth','uses' => 'EventController@EventDelete']);
  $router->post('duplicate-event',  ['middleware'=>'auth','uses' => 'EventController@duplicateEvent']);
	$router->post('get-event-tickets',  ['uses' => 'EventController@eventTickets']);
	$router->post('get-events-list',  ['uses' => 'EventController@eventsList']);
	//end events api
	
	//start country and currency api
	$router->post('get-country-api',  ['uses' => 'SaleTaxController@get_all_country']);
	$router->post('get-currancy-api',  ['uses' => 'SaleTaxController@get_all_currancy']);
	//end country and currency api

  //start customer APIs
	$router->post('create-customer-api',  ['middleware'=>'auth','uses' => 'CustomerController@Createcustomer']);
	$router->post('get-single-customer-api',  ['middleware'=>'auth','uses' => 'CustomerController@get_single_customer_data']);
	$router->post('get-all-customer-api',  ['middleware'=>'auth','uses' => 'CustomerController@get_all_customer_data']);
	$router->post('delete-customer-api',  ['middleware'=>'auth','uses' => 'CustomerController@CustomerDelete']);
	$router->post('update-customer-api',  ['middleware'=>'auth','uses' => 'CustomerController@CustomerUpdate']);
  $router->post('export-customers',  ['middleware'=>'auth','uses' => 'CustomerController@exportCustomers']);
  $router->post('import-customers',  ['middleware'=>'auth','uses' => 'CustomerController@importCustomers']);
  //end customer APIs

  //start forget & reset password
  $router->post('forget-password',  ['uses' => 'ForgetPasswordController@sendForgotEmail']);
  $router->post('reset-password',  ['uses' => 'ForgetPasswordController@resetPassword']);
	//end forget & reset password

  //start email verification
	$router->post('send-verification-email',  ['uses' => 'UsersController@sendVerificationEmail']);
	//$router->post('send-verification-email',  ['uses' => 'UsersController@sendVerificationEmail']);
  $router->post('get-profile-api', ['middleware'=>'auth','uses' => 'UsersController@get_profile_data']);
  $router->post('update-profile-api', ['middleware'=>'auth','uses' => 'UsersController@update_profile_data']);
  $router->post('remove-user-image', ['middleware'=>'auth','uses' => 'UsersController@userImageRemove']);
	//end email verification 

	//start coupons api
	$router->post('create-coupon-api',  ['middleware'=>'auth','uses' => 'CouponController@CreateCoupon']);
	$router->post('get-single-coupon-api',  ['middleware'=>'auth','uses' => 'CouponController@get_coupon_data']);
	$router->post('get-all-coupon-api',  ['uses' => 'CouponController@get_all_coupon_data']);
	$router->post('delete-coupon-api',  ['middleware'=>'auth','uses' => 'CouponController@CouponDelete']);
	$router->post('update-coupon-api',  ['middleware'=>'auth','uses' => 'CouponController@CouponUpdate']);
	$router->post('update-coupon-status-api',  ['middleware'=>'auth','uses' => 'CouponController@CouponStatusUpdate']);
	//end coupons api

	//start vouchers api
	$router->post('create-voucher-api',  ['middleware'=>'auth','uses' => 'VoucherController@Createvoucher']);
	$router->post('get-single-voucher-api',  ['middleware'=>'auth','uses' => 'VoucherController@get_voucher_data']);
	$router->post('get-all-voucher-api',  ['uses' => 'VoucherController@get_all_voucher_data']);
	$router->post('delete-voucher-api',  ['middleware'=>'auth','uses' => 'VoucherController@VoucherDelete']);
  $router->post('update-voucher-api',  ['middleware'=>'auth','uses' => 'VoucherController@VoucherUpdate']);
	$router->post('assign-voucher-to-event',  ['uses' => 'VoucherController@assignVoucherToEvent']);
	//end vouchers api
	
	//start broadcast api
	$router->post('create-broadcast-api',  ['middleware'=>'auth','uses' => 'BroadcastController@CreateBroadcast']);
	$router->post('get-single-broadcast-api',  ['middleware'=>'auth','uses' => 'BroadcastController@get_broadcast_data']);
	$router->post('get-all-broadcast-api',  ['middleware'=>'auth','uses' => 'BroadcastController@get_all_broadcast_data']);
	$router->post('delete-broadcast-api',  ['middleware'=>'auth','uses' => 'BroadcastController@BroadcastDelete']);
	$router->post('update-broadcast-api',  ['middleware'=>'auth','uses' => 'BroadcastController@BroadcastUpdate']);
	//end broadcast api

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
  $router->post('get-all-ticket',  ['middleware'=>'auth','uses' => 'TicketController@getAllTicket']);
  $router->post('delete-ticket',  ['middleware'=>'auth','uses' => 'TicketController@deleteEvent']);
  $router->post('coupon-apply-to-ticket',  ['uses' => 'TicketController@applyCoupon']);
  //end ticket APIs

  //get default images
  $router->post('get-default-images',  ['uses' => 'EventController@getDefaultImages']);

  //get languages
  $router->post('get-languages',  ['uses' => 'UsersController@getLanguages']);

  //strat  order confirmation api
	$router->post('set-order-confirmation-api',  ['middleware'=>'auth','uses' => 'EventDetailsController@createOrderConfirmation']);
	//end   order confirmation api

	//start checkout form api
	$router->post('set-checkout-form-api',  ['middleware'=>'auth','uses' => 'EventDetailsController@setCheckoutForm']);
	//end checkout form api 

  //get timeslots
  $router->post('get-timeslots',  ['uses' => 'EventController@getTimeslots']);

  //start order API
  $router->post('create-order',  ['uses' => 'OrderController@createOrder']);
  $router->post('get-single-order',  ['middleware'=>'auth','uses' => 'OrderController@getSingleOrder']);
  $router->post('get-all-order',  ['middleware'=>'auth','uses' => 'OrderController@getAllOrder']);
  $router->post('cancel-order',  ['middleware'=>'auth','uses' => 'OrderController@cancelOrder']);
  $router->post('export-orders',  ['middleware'=>'auth','uses' => 'OrderController@exportOrders']);
  $router->post('resend-order',  ['middleware'=>'auth','uses' => 'OrderController@ResendOrder']);
  $router->post('order-update',  ['middleware'=>'auth','uses' => 'OrderController@orderUpdate']);
  //end order API
  
  //start get setting option  api
	$router->post('get-setting-option-api',  ['uses' => 'SettingsController@getOptionValue']);
	//end get setting option api 

	//start get all setting option  api
	$router->post('get-all-setting-option-api',  ['uses' => 'SettingsController@getAllOptionsValue']);
	//end get all setting option api 

	//start set setting option  api
	$router->post('set-setting-option-api',  ['uses' => 'SettingsController@setOptionValue']);
	//end set setting option api 

	//start Inviter APIS
	$router->post('request-inviter-api',  ['middleware'=>'auth','uses' => 'InviterController@Createinviters']);
	$router->post('all-requested-inviter-api',  ['middleware'=>'auth','uses' => 'InviterController@get_inviters_data']);
  $router->post('delete-request-inviter-api',  ['middleware'=>'auth','uses' => 'InviterController@InviterDelete']);
  $router->get('accept-reject-invitation/{token}/{ans}',  ['uses' => 'InviterController@invitationAnswer']);
  $router->get('thank-you',  ['uses' => 'InviterController@thankyouPage']);
  $router->get('reject-invitation',  ['uses' => 'InviterController@rejectInvitation']);
  $router->get('already-accepted',  ['uses' => 'InviterController@alreadyAccepted']);
  $router->get('already-rejected',  ['uses' => 'InviterController@alreadyRejected']);
	$router->post('resend-invitation',  ['uses' => 'InviterController@resendInvitation']);
	//end inviter apis

	//waitlist api start
	$router->post('get-waiting-list',  ['uses' => 'WaitListController@getWaitList']);
  $router->post('create-waiting-list',  ['uses' => 'WaitListController@createWaitList']);
	$router->post('waiting-list',  ['uses' => 'WaitListController@getwaitingListUser']);
	//waitlist api end

	//get issue ticket API
	$router->post('get-all-issue-ticket',  ['middleware'=>'auth','uses' => 'IssueTicketController@getAllIssueTicket']);
	//get issue ticket API

	//get event summery api start
	$router->post('event-summery',  ['middleware'=>'auth','uses' => 'EventSummeryController@getEventSummery']);
	//get event summery api end

	//get doorlist details api start
	$router->post('export-doorlist',  ['middleware'=>'auth','uses' => 'DoorlistController@getAlldoorList']);
	//get doorlist details api end

  //start stripe api
  $router->post('stripe-payment',  ['uses' => 'StripeController@stripePayment']);
  //end stripe api

  //start invoice api
  $router->post('send-invoice',  ['uses' => 'InvoiceController@sendInvoice']);
  //end invoice api
});