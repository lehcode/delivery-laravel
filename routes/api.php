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

Route::group(['middleware' => 'api'], function() {
    Route::group(['middleware' => 'maintenance'], function() {
        Route::group(['prefix' => '{user_type}/v1'], function () {
            Route::any('authenticate',                                          ['uses' => 'AuthController@authenticate']);
            Route::get('user/me',                                               ['middleware' => 'jwt.auth',
                'uses' => 'AuthController@me']);
            Route::any('user/restore-password',                                 ['uses' => 'RestorePasswordController@sendLink']);
        });
    });
});
