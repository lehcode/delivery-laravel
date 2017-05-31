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
			Route::get('shipment-categories', ['middleware' => 'jwt.auth', 'uses' => 'ShipmentController@categories']);
		});

		/*
		 * Customer routes
		 */
		Route::group(['prefix' => 'customer/v1'], function () {

			Route::group(['prefix' => 'user'], function () {
				Route::post('edit', ['uses' => 'UserController@edit']);
			});

			Route::group(['middleware' => 'jwt.auth'], function () {
				Route::get('navigation', ['uses' => 'CustomerController@navigation']);
			});

			Route::group(['prefix' => 'user'], function () {
				Route::post('create', ['uses' => 'CustomerController@create']);

				Route::group(['middleware' => 'jwt.auth'], function () {
					Route::get('config', ['uses' => 'SettingsController@getConfig']);
					Route::post('config', ['uses' => 'SettingsController@update']);
				});
			});

			Route::group(['prefix' => 'order', 'middleware' => 'jwt.auth'], function () {
				Route::get('list', ['uses' => 'OrderController@getOrders']);
				Route::get('{id}', ['uses' => 'OrderController@getOrder']);
				Route::post('create', ['uses' => 'OrderController@createOrder']);
			});

		});

		/*
		 * Carrier routes
		 */
		Route::group(['prefix' => 'carrier/v1'], function () {

			Route::group(['prefix' => 'user'], function () {
				Route::post('edit', ['uses' => 'UserController@edit']);
			});

			Route::group(['prefix' => 'user'], function () {
				Route::post('create', ['uses' => 'CarrierController@create']);

				Route::group(['middleware' => 'jwt.auth'], function () {
					Route::get('config', ['uses' => 'SettingsController@getConfig']);
					Route::post('config', ['uses' => 'SettingsController@update']);
				});
			});

			Route::group(['middleware' => 'jwt.auth'], function () {
				Route::get('navigation', ['uses' => 'CarrierController@navigation']);

				Route::group(['prefix' => 'trip'], function () {
					Route::post('create', ['uses' => 'TripController@createTrip']);
					Route::get('list', ['uses' => 'TripController@getTrips']);
					Route::get('{trip_id}', ['uses' => 'TripController@getTrip']);
				});
			});

			Route::group(['middleware' => 'jwt.auth'], function () {
				Route::group(['prefix' => 'order'], function () {
					Route::get('orders', ['uses' => 'OrderController@getOrders']);
					Route::get('{id}', ['uses' => 'OrderController@getOrder']);
				});
			});

		});
	});
});
