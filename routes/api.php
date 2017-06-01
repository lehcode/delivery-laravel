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
		/*
		 * Common role-depending routes
		 */
		Route::group(['prefix' => '{user_type}/v1'], function () {

			Route::post('authenticate', ['uses' => 'AuthController@authenticate']);

			Route::group(['middleware' => 'jwt.auth'], function () {
				Route::get('shipment-categories', ['uses' => 'ShipmentController@categories']);

				Route::group(['prefix' => 'user'], function () {
					Route::any('me', ['uses' => 'AuthController@me']);
					Route::post('restore-password', ['uses' => 'RestorePasswordController@sendLink']);
				});

				Route::group(['prefix' => 'info'], function () {
					Route::get('help', ['uses' => 'InfoController@help']);
					Route::get('about', ['uses' => 'InfoController@about']);
					Route::get('legal', ['uses' => 'InfoController@legal']);
				});
			});

		});

		/*
		 * Customer routes
		 */
		Route::group(['prefix' => 'customer/v1'], function () {

			Route::group(['prefix' => 'user'], function () {
				Route::post('create', ['uses' => 'CustomerController@create']);
			});

			Route::group(['middleware' => 'jwt.auth'], function () {

				Route::group(['prefix' => 'user'], function () {
					Route::get('navigation', ['uses' => 'CustomerController@navigation']);
					Route::post('edit', ['uses' => 'UserController@edit']);

					Route::get('setings', ['uses' => 'SettingsController@getSettings']);
					Route::post('setings', ['uses' => 'SettingsController@updateSettings']);

					Route::get('payment-info', ['uses'=>'PaymentController@getUserPaymentInfo']);
					Route::post('payment-info', ['uses'=>'PaymentController@storeUserPaymentInfo']);
				});

				Route::group(['prefix' => 'order'], function () {
					Route::get('all', ['uses' => 'OrderController@all']);
					Route::get('active', ['uses' => 'OrderController@active']);
					Route::get('{id}', ['uses' => 'OrderController@get']);
					Route::post('create', ['uses' => 'OrderController@create']);
				});

				Route::group(['prefix' => 'trip'], function () {
					Route::get('all', ['uses' => 'TripController@fromCurrentCity']);
					Route::get('{trip_id}', ['uses' => 'TripController@get']);
				});

				Route:: group(['prefix'=>'payment'], function(){
					Route::post('create', ['uses'=>'PaymentController@createPayment']);
				});
			});

		});

		/*
		 * Carrier routes
		 */
		Route::group(['prefix' => 'carrier/v1'], function () {

			Route::group(['prefix' => 'user'], function () {
				Route::post('create', ['uses' => 'CarrierController@create']);
			});

			Route::group(['middleware' => 'jwt.auth'], function () {

				Route::group(['prefix' => 'user'], function () {
					Route::get('navigation', ['uses' => 'CarrierController@navigation']);
					Route::post('edit', ['uses' => 'UserController@edit']);
					Route::get('settings', ['uses' => 'SettingsController@get']);
					Route::post('settings', ['uses' => 'SettingsController@update']);
				});

				Route::group(['prefix' => 'trip'], function () {
					//Route::get('my-trips', ['uses' => 'CarrierController@getUserTrips']);
					Route::get('active-trips', ['uses' => 'CarrierController@getActiveTrips']);

					Route::get('all', ['uses' => 'TripController@all']);
					Route::get('{trip_id}', ['uses' => 'TripController@get']);
					Route::post('create', ['uses' => 'TripController@create']);
				});

				Route::group(['prefix' => 'order'], function () {
					Route::get('all', ['uses' => 'OrderController@all']);
					Route::get('{id}', ['uses' => 'OrderController@get']);
				});

				Route::group(['prefix'=>'payment'], function(){
					Route::get('types', ['uses' => 'PaymentController@types']);
				});

			});

		});
	});
});
