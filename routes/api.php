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

Route::group(['middleware' => 'auth:api'], function() {
	Route::get('/user', function() {
		dd('hola');
	});
});
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
