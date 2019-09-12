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

// Route::resources([
//     'users' => 'UserController'
// ]);

// Route::group(['middleware' => ['auth:api']], function () {
	Route::get('users', 'v1\UserController@list');
    Route::post('users', 'v1\UserController@create');
	Route::get('users/{user}', 'v1\UserController@show')->where(['user' => '^([1-9]|[1-9][0-9]*)$']);
    Route::put('users/{user}', 'v1\UserController@update')->where(['user' => '^([1-9]|[1-9][0-9]*)$']);
    Route::delete('users/{user}', 'v1\UserController@destroy')->where(['user' => '^([1-9]|[1-9][0-9]*)$']);
    Route::patch('users/{user}/restore', 'v1\UserController@restore')->where(['user' => '^([1-9]|[1-9][0-9]*)$']);
    
	// Route::get('/create', 'PostController@create')->name('create');
	// Route::post('/store', 'PostController@store')->name('store');
// });
Route::post('users/forgot_password', 'v1\UserController@forgotPassword');
Route::post('users/reset_password', 'v1\UserController@resetPassword');

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
