<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'api'], function () {

	Route::group(['middleware' => 'maintenance'], function () {
		Route::group(['prefix' => '{user_type}/v1'], function () {
			Route::post('authenticate', ['uses' => 'AuthController@authenticate']);
			Route::post('user/restore-password', ['uses' => 'RestorePasswordController@sendLink']);
			Route::any('user/me', ['middleware' => 'jwt.auth', 'uses' => 'AuthController@me']);
			Route::get('user/navigation', ['middleware' => 'jwt.auth', 'uses' => 'AuthController@navigation']);
		});


		Route::group(['prefix' => 'customer/v1'], function () {
			Route::group(['middleware' => 'jwt.auth'], function(){
				Route::get('navigation', ['uses' => 'CustomerController@navigation']);

				Route::group(['prefix' => 'user'], function () {
					Route::post('edit', ['uses' => 'UserController@edit']);
				});
			});

			Route::group(['prefix' => 'user'], function () {
				Route::post('create', ['uses' => 'CustomerController@create']);
			});
		});


		Route::group(['prefix' => 'carrier/v1'], function () {

			Route::group(['prefix' => 'user'], function () {
				Route::post('create', ['uses' => 'CarrierController@create']);
			});

			Route::group(['middleware' => 'jwt.auth'], function(){
				Route::get('navigation', ['uses' => 'CarrierController@navigation']);

				Route::group(['prefix' => 'user'], function () {
					Route::post('edit', ['uses' => 'UserController@edit']);
				});

				Route::group(['prefix' => 'trip'], function () {
					Route::get('list', ['uses' => 'TripController@getTrips']);
					Route::get('{trip}', ['uses' => 'TripController@item']);
					Route::get('{trip}/available_time', ['uses' => 'TripController@getAvailableTimes']);
				});

			});
		});
	});
});
