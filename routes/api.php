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
    // Users
    Route::get('users', 'v1\UserController@index');
    Route::get('users/{user}', 'v1\UserController@show');
    Route::post('users', 'v1\UserController@create');
    Route::put('users/{user}', 'v1\UserController@create');
    Route::patch('users/{user}/restore', 'v1\UserController@restore');
    Route::delete('users/{user}', 'v1\UserController@destroy');

    // Roles
    Route::get('roles', 'v1\RoleController@index');
    // Super endpoints
    Route::group(['middleware' => 'role:' . config('settings.user.rootRole')], function () {
        Route::post('roles', 'v1\RoleController@create');
        Route::put('roles/{role}', 'v1\RoleController@create');
        Route::put('roles/{role}/permissions', 'v1\RoleController@assignPermissionsToRole');
        Route::delete('roles/{role}', 'v1\RoleController@destroy');
    });

    //Permissions
    Route::get('permissions', 'v1\RoleController@permissions');
});
