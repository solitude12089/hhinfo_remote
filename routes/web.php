<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

// Route::get('login', function () {
//     return view('auth.login');
// })->name('login');


Route::get('/login', 'Auth\LoginController@getLogin');
Route::post('/login', 'Auth\LoginController@postLogin');
Route::get('/logout', 'Auth\LoginController@getLogout');

// Route::get('logout', function () {
//     Auth::logout();
// 	return redirect('/login');
// })->name('logout');



Route::group(['middleware' => 'auth'], function(){

	//Account
 	Route::group(['prefix' => 'account'], function () {
 		Route::get('/reset', 'AccountController@getReset');
 		Route::post('/reset', 'AccountController@postReset');
 	});

 	//Admin
 	Route::group(['prefix' => 'admin'], function () {
		Route::get('/account','AdminController@getAccount');
		Route::get('/account/create','AdminController@getCreateAccount');
		Route::post('/account/create','AdminController@postCreateAccount');
		Route::get('/account/edit/{id}','AdminController@getEditAccount');
		Route::post('/account/edit/{id}','AdminController@postEditAccount');

		Route::get('/group','AdminController@getGroup');
		Route::get('/group/create','AdminController@getCreateGroup');
		Route::post('/group/create','AdminController@postCreateGroup');
		Route::get('/group/edit/{id}','AdminController@getEditGroup');
		Route::post('/group/edit/{id}','AdminController@postEditGroup');

		Route::get('/device','AdminController@getDevice');
		Route::get('/device/create','AdminController@getCreateDevice');
		Route::post('/device/create','AdminController@postCreateDevice');
		Route::get('/device/edit/{id}','AdminController@getEditDevice');
		Route::post('/device/edit/{id}','AdminController@postEditDevice');


 	});
	//Customer
	Route::group(['prefix' => 'customer'], function (){
		Route::get('/index','CustomerController@index');
		Route::get('/create','CustomerController@create');
		Route::get('{id}/edit','CustomerController@edit');
		Route::post('{id}/update','CustomerController@update');
		Route::post('/store','CustomerController@store');
		Route::post('/list','CustomerController@list');
		Route::get('/spcard','CustomerController@spcard');
		Route::get('/create-spcard','CustomerController@getcreatespcard');
		Route::post('/create-spcard','CustomerController@postcreatespcard');
		Route::get('/edit-spcard/{id}','CustomerController@geteditspcard');
		Route::post('/edit-spcard/{id}','CustomerController@posteditspcard');
		Route::post('/remove-spcard','CustomerController@postremovespcard');
		Route::post('/checkcardid','CustomerController@checkcardid');
	
	});



	//Remote
	Route::group(['prefix' => 'remote'], function () {
		Route::get('/test','RemoteController@test');
		Route::get('/index','RemoteController@index');

		Route::get('/status/{id}','RemoteController@getStatus');
		Route::get('/set-status/{id}','RemoteController@setStatus');
		Route::get('/time/{id}','RemoteController@getTime');
		Route::get('/set-time/{id}','RemoteController@setTime');
		
	});

	//Booking
	Route::group(['prefix' => 'booking'], function () {
		Route::get('/index','BookingController@index');
		Route::post('/search','BookingController@postSearch');
		Route::post('/booking','BookingController@postBooking');

		Route::get('/query','BookingController@getQuery');
		Route::post('/query','BookingController@postQuery');

		Route::get('/calendar','BookingController@getCalendar');
		Route::post('/calendar','BookingController@postCalendar');

		Route::post('/remove','BookingController@remove');
	});

	//SystemLog
	Route::group(['prefix' => 'systemlog'], function () {
		Route::get('/index','SystemLogController@index');
	});



	Route::get('/', function () {
    	return view('welcome');
	});
	 
});


Route::group(['prefix' => '/api/v1'], function () {
		// Route::get('/customer/test', '\App\Http\Controllers\Api\v1\CustomerController@test');
 		Route::post('/phone/registered', '\App\Http\Controllers\Api\v1\PhoneController@registered');
		Route::post('/phone/verify', '\App\Http\Controllers\Api\v1\PhoneController@verify');
		Route::post('/phone/menu', '\App\Http\Controllers\Api\v1\PhoneController@menu');
		Route::post('/phone/btnclick', '\App\Http\Controllers\Api\v1\PhoneController@btnclick');

 		Route::get('/remote/dcode', '\App\Http\Controllers\Api\v1\RemoteController@dcode');
 		Route::get('/remote/operdo', '\App\Http\Controllers\Api\v1\RemoteController@operdo');
 	
});



