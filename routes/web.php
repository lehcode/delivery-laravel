<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/reset-password/done', ['as' => 'password_reset.success', 'uses' => 'RestorePasswordController@success']);
Route::get('/reset-password/{key}', ['as' => 'password_reset.form', 'uses' => 'RestorePasswordController@restorePage']);
Route::post('/reset-password/{key}', ['as' => 'password_reset.action', 'uses' => 'RestorePasswordController@restoreAction']);

Route::get('/activate/{user}/{key}', ['as' => 'activate.link', 'uses' => 'UserController@verify']);
