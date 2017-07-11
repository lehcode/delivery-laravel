<?php
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


Route::group(['middleware' => ['maintenance', 'api']], function () {

	/*
	 * Common role-depending routes
	 */
	Route::group(['prefix' => '{user_type}/v1'], function () {

		Route::post('authenticate', ['uses' => 'AuthController@authenticate']);

		Route::group(['middleware' => 'jwt.auth'], function () {


			Route::get('city/{city_id}', ['uses' => 'TripController@getCity']);
			Route::get('city/find/{search}/{country_code}', ['uses' => 'TripController@findCityByName']);
			Route::get('cities', ['uses' => 'TripController@getCities']);

			Route::group(['prefix' => 'shipment'], function () {
				Route::get('sizes', ['uses' => 'ShipmentController@sizes']);
				Route::get('categories', ['uses' => 'ShipmentController@categories']);
			});

			Route::group(['prefix' => 'user'], function () {
				Route::any('me', ['uses' => 'AuthController@me']);
				Route::post('reset-password', ['uses' => 'AuthController@sendLink']);
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

				Route::get('setings', ['uses' => 'SettingsController@getSettings']);
				Route::post('setings', ['uses' => 'SettingsController@updateSettings']);

				Route::get('payment-info', ['uses' => 'CustomerController@getPaymentInfo']);
				Route::post('payment-info', ['uses' => 'CustomerController@storePaymentInfo']);
				Route::patch('payment-info/update', ['uses' => 'CustomerController@updatePaymentInfo']);

				Route::post('update', ['uses' => 'CustomerController@update']);
			});

			Route::group(['prefix' => 'order'], function () {
				Route::get('all', ['uses' => 'OrderController@getCustomerOrders']);
				Route::get('active', ['uses' => 'OrderController@getActiveOrders']);
				Route::get('{id}', ['uses' => 'OrderController@getOrder']);
				Route::post('create', ['uses' => 'OrderController@createOrder']);
				Route::patch('update/{id}', ['uses' => 'OrderController@updateOrder']);
				Route::get('find/shipment-type/{id}', ['uses' => 'OrderController@findOrderByShipmentType']);
				Route::post('recipient/create', ['uses' => 'OrderController@createRecipient']);
				Route::post('shipment/create', ['uses' => 'ShipmentController@createShipment']);
			});

			Route::group(['prefix' => 'trip'], function () {
				Route::get('all', ['uses' => 'TripController@fromCurrentCity']);
				Route::get('from-city/{city}', ['uses' => 'TripController@fromCity']);
				Route::get('{trip_id}', ['uses' => 'TripController@get']);
				Route::get('find/start/{start_date}/end/{end_date}', ['uses' => 'TripController@getByDatePeriod']);
				Route::get('find/date/{date}', ['uses' => 'TripController@getByDate']);
			});

			Route:: group(['prefix' => 'payment'], function () {
				Route::post('create', ['uses' => 'PaymentController@createPayment']);
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
				Route::patch('update', ['uses' => 'CarrierController@update']);
				Route::get('settings', ['uses' => 'SettingsController@get']);
				Route::post('settings', ['uses' => 'SettingsController@update']);
			});

			Route::group(['prefix' => 'trip'], function () {
				Route::get('my', ['uses' => 'CarrierController@getUserTrips']);
				Route::get('active-trips', ['uses' => 'CarrierController@getActiveTrips']);
				Route::get('{trip_id}', ['uses' => 'TripController@get']);
				Route::post('create', ['uses' => 'TripController@create']);
			});

			Route::group(['prefix' => 'order'], function () {
				Route::get('all', ['uses' => 'OrderController@getCarrierOrders']);
				Route::get('{id}', ['uses' => 'OrderController@get']);
			});

			Route::group(['prefix' => 'payment'], function () {
				Route::get('types', ['uses' => 'PaymentController@types']);
			});

		});

	});
});
