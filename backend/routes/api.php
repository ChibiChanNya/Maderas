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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('auth')->group(function () {
    // Below mention routes are public, user can access those without any restriction.
    // Create New User
    // Route::post('register', 'AuthController@register');
    // Login User
    Route::post('login', 'AuthController@login');
    // Refresh the JWT Token
    Route::post('refresh', 'AuthController@refresh');
    
    // Below mention routes are available only for the authenticated users.
    Route::middleware('auth:api')->group(function () {
        // Get user info
        // Route::post('me', 'AuthController@me');
        // Logout user from application
        Route::post('logout', 'AuthController@logout');
    });
});

Route::prefix('user')->group(function () {
    // Route::post('register', 'UserController@register');
    // Below mention routes are available only for the authenticated users.
    Route::middleware('auth:api')->group(function () {
        // Create New User
        Route::post('register', 'UserController@register');
        // Get user info
        Route::get('me', 'UserController@me');
        // Get all users
        Route::get('list', 'UserController@users_list');
        // Update a user
        Route::post('update', 'UserController@user_update');
        // Delete a user
        Route::post('delete', 'UserController@user_delete');

        // Log part
        Route::get('all_log', 'UserController@all_log');
        Route::get('user_log', 'UserController@user_log');
    });
});

// Route::group([
//     'prefix' => 'auth'
// ], function ($router) {

//     Route::post('login', 'AuthController@login');
//     Route::post('logout', 'AuthController@logout');
//     Route::post('refresh', 'AuthController@refresh');
//     Route::post('me', 'AuthController@me');
//     Route::post('/register', 'AuthController@register');

// });

Route::get('prueba', 'AuthController@prueba')->middleware('web');
