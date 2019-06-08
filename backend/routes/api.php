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
        Route::get('user_log/{id}', 'UserController@user_log');
    });
});

Route::prefix('providers')->group(function () {
    // Route::post('register', 'UserController@register');
    // Below mention routes are available only for the authenticated users.
    Route::middleware('auth:api')->group(function () {
        // Create New Provider
        Route::post('create', 'ProviderController@create');
        // Get all Providers
        Route::get('list', 'ProviderController@providers_list');
        // Update a Provider
        Route::post('update', 'ProviderController@provider_update');
        // Delete a Provider
        Route::post('delete', 'ProviderController@provider_delete');
    });
});

Route::prefix('materials')->group(function () {
    // Route::post('register', 'UserController@register');
    // Below mention routes are available only for the authenticated users.
    Route::middleware('auth:api')->group(function () {
        // Create New User
        Route::post('create', 'SupplyController@create');
        // Get all Supplies
        Route::get('list', 'SupplyController@supplies_list');
        // Update a Supply
        Route::post('update', 'SupplyController@supply_update');
        // Delete a Supply
        Route::post('delete', 'SupplyController@supply_delete');
    });
});

Route::prefix('orders/providers')->group(function () {
    // Route::post('register', 'UserController@register');
    // Below mention routes are available only for the authenticated users.
    Route::middleware('auth:api')->group(function () {
        // Create New Order
        Route::post('create', 'OrderToProviderController@create');
        // Get all Orders
        Route::get('list', 'OrderToProviderController@orders_list');
        // Update an Order
        Route::post('update', 'OrderToProviderController@order_update');
        // Delete an Order
        Route::post('delete', 'OrderToProviderController@order_delete');
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
