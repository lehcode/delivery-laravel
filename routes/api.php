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

		/*
		 * Customer routes
		 */
		Route::group(['prefix' => 'customer/v1'], function () {

			Route::group(['prefix' => 'user'], function () {
				Route::post('edit', ['uses' => 'UserController@edit']);
			});

			Route::group(['middleware' => 'jwt.auth', 'namespace' => 'Customer'], function () {
				Route::get('navigation', ['uses' => 'CustomerController@navigation']);
			});

			Route::group(['prefix' => 'user', 'namespace' => 'Customer'], function () {
				Route::post('create', ['uses' => 'CustomerController@create']);
			});

			Route::group(['prefix' => 'order', 'namespace' => 'Customer'], function () {
				Route::get('list', ['uses' => 'OrderController@orders']);
				Route::get('{order}', ['uses' => 'OrderController@order']);
			});

			Route::group(['prefix' => 'trip', 'namespace' => 'Customer'], function () {
				Route::get('list', ['uses' => 'OrderController@trips']);
				Route::get('{order}', ['uses' => 'OrderController@trip']);
			});
		});

		/*
		 * Carrier routes
		 */
		Route::group(['prefix' => 'carrier/v1'], function () {

			Route::group(['prefix' => 'user'], function () {
				Route::post('edit', ['uses' => 'UserController@edit']);
			});

			Route::group(['prefix' => 'user', 'namespace' => 'Carrier'], function () {
				Route::post('create', ['uses' => 'CarrierController@create']);
			});

			Route::group(['middleware' => 'jwt.auth', 'namespace' => 'Carrier'], function () {
				Route::get('navigation', ['uses' => 'CarrierController@navigation']);

				Route::group(['prefix' => 'trip', 'namespace' => 'Carrier'], function () {
					Route::get('list', ['uses' => 'TripController@list']);
					Route::get('{trip}', ['uses' => 'TripController@trip']);
				});

			});
		});
	});
});
