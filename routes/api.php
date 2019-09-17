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

Route::post('login', 'v1\AuthController@login');
Route::post('users/forgot_password', 'v1\UserController@forgotPassword');
Route::post('users/reset_password', 'v1\UserController@resetPassword');

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('users', 'v1\UserController@index');
    Route::get('users/{user}', 'v1\UserController@show')->where(['user' => '^([1-9]|[1-9][0-9]*)$']);
    Route::post('users', 'v1\UserController@create');
    Route::put('users/{user}', 'v1\UserController@create')->where(['user' => '^([1-9]|[1-9][0-9]*)$']);
    Route::patch('users/{user}/restore', 'v1\UserController@restore')->where(['user' => '^([1-9]|[1-9][0-9]*)$']);
    Route::delete('users/{user}', 'v1\UserController@destroy')->where(['user' => '^([1-9]|[1-9][0-9]*)$']);
    Route::post('users/{user}/upload', 'v1\UserController@uploadPicture')->where(['user' => '^([1-9]|[1-9][0-9]*)$']);
});
