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
	Route::resource('/customer','CustomerController');
	Route::post('/customer/list','CustomerController@list');

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
	});


	Route::get('/', function () {
    	return view('welcome');
	});
	 
});


Route::group(['prefix' => '/api/v1'], function () {
		Route::get('/customer/test', '\App\Http\Controllers\Api\v1\CustomerController@test');
 		Route::post('/customer/registered', '\App\Http\Controllers\Api\v1\CustomerController@registered');
 		Route::post('/customer/verify', '\App\Http\Controllers\Api\v1\CustomerController@verify');


 		Route::get('/remote/dcode', '\App\Http\Controllers\Api\v1\RemoteController@dcode');
 		Route::get('/remote/operdo', '\App\Http\Controllers\Api\v1\RemoteController@operdo');
 		Route::get('/remote/test', '\App\Http\Controllers\Api\v1\RemoteController@test');


 		Route::get('/remote/api1', '\App\Http\Controllers\Api\v1\RemoteController@api1');
 		Route::get('/remote/api2', '\App\Http\Controllers\Api\v1\RemoteController@api2');
 		Route::get('/remote/api3-get', '\App\Http\Controllers\Api\v1\RemoteController@api3Get');
 		Route::get('/remote/api3-set', '\App\Http\Controllers\Api\v1\RemoteController@api3Set');
});

